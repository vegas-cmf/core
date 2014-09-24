<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */ 

namespace Vegas\Tests\Mvc;

use Vegas\Mvc\View;
use Vegas\Tests\App\TestCase;

class ViewTest extends TestCase
{
    public function testEngineRegistration()
    {
        $view = new View();

        $this->assertSame(2, count(array_keys($view->getRegisteredEngines())));
        $this->assertArrayHasKey('.volt', $view->getRegisteredEngines());
        $this->assertArrayHasKey('.phtml', $view->getRegisteredEngines());
    }

    public function testViewVars()
    {
        $view = new View();

        $view->testValue = 123;
        $this->assertEquals(123, $view->getVar('testValue'));
        $view->setVar('testValue', 456);
        $this->assertEquals(456, $view->testValue);
    }

    public function testViewOptions()
    {
        $options = array(
            'layout'    =>  'main.volt',
            'layoutsDir'    =>  APP_ROOT.'/app/layouts/'
        );
        $view = new View($options, __DIR__);

        $this->assertEquals('main.volt', $view->getLayout());
        $this->assertNotEmpty($view->getLayoutsDir());
    }

    public function testPathsResolving()
    {
        $configView = $this->di->get('config')->application->view->toArray();
        if (!file_exists($configView['cacheDir'])) {
            mkdir($configView['cacheDir'], 0777);
        } else {
            chmod($configView['cacheDir'], 0777);
        }

        $content = function($params) {
            $this->setUp();
            $route = $this->bootstrap->getDI()->get('router')->getRouteByName('test');
            $url = rtrim(str_replace(array(':action', ':params'), $params, $route->getPattern()), DIRECTORY_SEPARATOR);
            $this->bootstrap->run($url);
            return $this->bootstrap->getDI()->get('response')->getContent();
        };

        //compares output rendered by dispatcher
        //views are loaded in the following order:
        //app/layouts/main.volt     =>  1
        //app/layouts/partials/test/sample.volt     => 2
        //app/modules/Test/views/frontend/fake/test.volt    =>  3
        //app/modules/Test/views/frontend/fake/partials/test.volt    =>  4
        //output of dispatcher => 1234
        $response = $content(array('test', ''));
        $this->assertEquals('1234', $response);

        //tests rendering only layout with one partial
        //should return 12 regarding to upwards comments
        $response = $content(array('testLayout', ''));
        $this->assertEquals('12', $response);

        //test rendering only view with one partial
        //should return 34 regarding to upwards comments
        $response = $content(array('testView', ''));
        $this->assertEquals('34', $response);

        //test rendering only global partial
        //should return 2 regarding to upwards comments
        $response = $content(array('testGlobal', ''));
        $this->assertEquals('2', $response);

        //extract volt engine
        $view = $this->bootstrap->getDI()->get('view');

        ob_start();
        $view->partial('test/sample');
        $this->assertEquals('2', ob_get_contents());
        ob_end_clean();

        ob_start();
        $view->partial(APP_ROOT . '/app/layouts/partials/test/sample');
        $this->assertEquals('2', ob_get_contents());
        ob_end_clean();

        ob_start();
        $view->partial('./frontend/fake/partials/test');
        $this->assertEquals('4', ob_get_contents());
        ob_end_clean();

        ob_start();
        $view->partial('./frontend/other/partials/other');
        $this->assertEquals('5', ob_get_contents());
        ob_end_clean();

        ob_start();
        $view->partial('../../../layouts/partials/test/sample');
        $this->assertEquals('2', ob_get_contents());
        ob_end_clean();

        //tests view located outside of `app` directory
        ob_start();
        $view->partial(APP_ROOT . '/test');
        $this->assertEquals('OUTSIDER', ob_get_contents());
        ob_end_clean();
    }

    public function testPathsResolvingWithoutPartialsDirInConfig()
    {
        $configView = $this->di->get('config')->application->view->toArray();
        if (!file_exists($configView['cacheDir'])) {
            mkdir($configView['cacheDir'], 0777);
        } else {
            chmod($configView['cacheDir'], 0777);
        }
        $content = function($params) {
            $this->setUp();
            $this->bootstrap->getDI()->get('config')->application->view->partialsDir = false;
            $this->bootstrap->getDI()->get('config')->application->view->layout = 'main2';
            $route = $this->bootstrap->getDI()->get('router')->getRouteByName('testfoo');
            $url = rtrim(str_replace(array(':action', ':params'), $params, $route->getPattern()), DIRECTORY_SEPARATOR);
            $this->bootstrap->run($url);
            return $this->bootstrap->getDI()->get('response')->getContent();
        };

        //compares output rendered by dispatcher
        //views are loaded in the following order:
        //app/layouts/main.volt     =>  1
        //app/layouts/partials/test/sample.volt     => 2
        //app/modules/Test/views/frontend/foo/test.volt    =>  3
        //app/modules/Test/views/frontend/foo/partials/test.volt    =>  4
        //output of dispatcher => 1234
        $response = $content(array('test', ''));
        $this->assertEquals('1234', $response);

        //tests rendering only layout with one partial
        //should return 12 regarding to upwards comments
        $response = $content(array('testLayout', ''));
        $this->assertEquals('12', $response);

        //test rendering only view with one partial
        //should return 34 regarding to upwards comments
        $response = $content(array('testView', ''));
        $this->assertEquals('34', $response);

        //test rendering only local partial
        //should return 4 regarding to upwards comments
        $response = $content(array('testLocal', ''));
        $this->assertEquals('4', $response);

        //extract volt engine
        $view = $this->bootstrap->getDI()->get('view');

        ob_start();
        $view->partial('../../../layouts/partials/test/sample');
        $this->assertEquals('2', ob_get_contents());
        ob_end_clean();

        ob_start();
        $view->partial(APP_ROOT . '/app/layouts/partials/test/sample');
        $this->assertEquals('2', ob_get_contents());
        ob_end_clean();

        ob_start();
        $view->partial('./frontend/foo/partials/test');
        $this->assertEquals('4', ob_get_contents());
        ob_end_clean();

        ob_start();
        $view->partial('./frontend/other/partials/other');
        $this->assertEquals('5', ob_get_contents());
        ob_end_clean();

        ob_start();
        $view->partial('../../../layouts/partials/test/sample');
        $this->assertEquals('2', ob_get_contents());
        ob_end_clean();

        //tests view located outside of `app` directory
        ob_start();
        $view->partial(APP_ROOT . '/test');
        $this->assertEquals('OUTSIDER', ob_get_contents());
        ob_end_clean();
    }
}
 