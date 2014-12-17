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
use Vegas\Test\TestCase;

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
            'layoutsDir'    =>  APP_ROOT.'/app/layouts/',
            'compileAlways' => true
        );
        $view = new View($options, __DIR__);

        $this->assertEquals('main.volt', $view->getLayout());
        $this->assertNotEmpty($view->getLayoutsDir());
    }

    public function testPathsResolving()
    {
        $configView = $this->getDI()->get('config')->application->view->toArray();

        $getContent = function($params) {
            $this->setUp();
            $route = $this->getDI()->get('router')->getRouteByName('test');
            $url = rtrim(str_replace(array(':action', ':params'), $params, $route->getPattern()), DIRECTORY_SEPARATOR);
            return $this->handleUri($url)->getContent();
        };

        //compares output rendered by dispatcher
        //views are loaded in the following order:
        //app/layouts/main.volt     =>  1
        //app/layouts/partials/test/sample.volt     => 2
        //app/modules/Test/views/frontend/fake/test.volt    =>  3
        //app/modules/Test/views/frontend/fake/partials/test.volt    =>  4
        //output of dispatcher => 1234
        $response = $getContent(array('test', ''));
        $this->assertEquals('1234', $response);

        //tests rendering only layout with one partial
        //should return 12 regarding to upwards comments
        $response = $getContent(array('testLayout', ''));
        $this->assertEquals('12', $response);

        //test rendering only view with one partial
        //should return 34 regarding to upwards comments
        $response = $getContent(array('testView', ''));
        $this->assertEquals('34', $response);

        //test rendering only global partial
        //should return 2 regarding to upwards comments
        $response = $getContent(array('testGlobal', ''));
        $this->assertEquals('2', $response);

        //extract volt engine
        $view = $this->getDI()->get('view');

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

        //reverts view configuration
        $this->getDI()->get('config')->application->view = $configView;
    }

    public function testPathsResolvingWithoutPartialsDirInConfig()
    {
        $configView = $this->getDI()->get('config')->application->view->toArray();

        $getContent = function($params) {
            $this->setUp();
            $this->getDI()->get('config')->application->view->partialsDir = false;
            $this->getDI()->get('config')->application->view->layout = 'main2';
            $route = $this->getDI()->get('router')->getRouteByName('testfoo');
            $url = rtrim(str_replace(array(':action', ':params'), $params, $route->getPattern()), DIRECTORY_SEPARATOR);
            return $this->handleUri($url)->getContent();
        };

        //compares output rendered by dispatcher
        //views are loaded in the following order:
        //app/layouts/main.volt     =>  1
        //app/layouts/partials/test/sample.volt     => 2
        //app/modules/Test/views/frontend/foo/test.volt    =>  3
        //app/modules/Test/views/frontend/foo/partials/test.volt    =>  4
        //output of dispatcher => 1234
        $response = $getContent(array('test', ''));
        $this->assertEquals('1234', $response);

        //tests rendering only layout with one partial
        //should return 12 regarding to upwards comments
        $response = $getContent(array('testLayout', ''));
        $this->assertEquals('12', $response);

        //test rendering only view with one partial
        //should return 34 regarding to upwards comments
        $response = $getContent(array('testView', ''));
        $this->assertEquals('34', $response);

        //test rendering only local partial
        //should return 4 regarding to upwards comments
        $response = $getContent(array('testLocal', ''));
        $this->assertEquals('4', $response);

        //extract volt engine
        $view = $this->getDI()->get('view');

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

        //reverts view configuration
        $this->getDI()->get('config')->application->view = $configView;
    }

    public function testShortNamespacePathsResolving()
    {
        $configView = $this->getDI()->get('config')->application->view->toArray();

        $getContent = function($params) {
            $this->setUp();
            $route = $this->getDI()->get('router')->getRouteByName('testshort');
            $url = rtrim(str_replace(array(':action', ':params'), $params, $route->getPattern()), DIRECTORY_SEPARATOR);
            return $this->handleUri($url)->getContent();
        };

        //compares output rendered by dispatcher
        //views are loaded in the following order:
        //app/layouts/main.volt     =>  1
        //app/layouts/partials/test/sample.volt     => 2
        //app/modules/Test/views/fake/test.volt    =>  3
        //app/modules/Test/views/fake/partials/test.volt    =>  4
        //output of dispatcher => 1234
        $response = $getContent(array('test', ''));
        $this->assertEquals('12shortPartialShort', $response);

        //tests rendering only layout with one partial
        //should return 12 regarding to upwards comments
        $response = $getContent(array('testLayout', ''));
        $this->assertEquals('12', $response);

        //test rendering only view with one partial
        //should return shortPartialShort regarding to upwards comments
        $response = $getContent(array('testView', ''));
        $this->assertEquals('shortPartialShort', $response);

        //extract volt engine
        $view = $this->getDI()->get('view');

        ob_start();
        $view->partial('test/sample');
        $this->assertEquals('2', ob_get_contents());
        ob_end_clean();

        ob_start();
        $view->partial(APP_ROOT . '/app/layouts/partials/test/sample');
        $this->assertEquals('2', ob_get_contents());
        ob_end_clean();

        ob_start();
        $view->partial('./fake/partials/test');
        $this->assertEquals('PartialShort', ob_get_contents());
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

    public function testViewCaching()
    {
        $this->getDI()->get('config')->application->view->compileAlways = false;
        $configView = $this->getDI()->get('config')->application->view->toArray();

        $view = new View($configView);
        $this->getDI()->set('view', function() use ($view) { return $view; });

        if (!file_exists($configView['cacheDir'])) {
            mkdir($configView['cacheDir'], 0777);
        } else {
            chmod($configView['cacheDir'], 0777);
        }

        $getContent = function() {
            $this->setUp();
            $route = $this->getDI()->get('router')->getRouteByName('test');
            $url = rtrim(str_replace([':action', ':params'], ['test', ''], $route->getPattern()), DIRECTORY_SEPARATOR);
            return $this->handleUri($url)->getContent();
        };
        $getContent();

        $cacheFileKey = str_replace('/', '_', APP_ROOT) . '_app_modules_test_views_frontend_fake_test.volt.php';
        $this->assertFileExists($configView['cacheDir'] . $cacheFileKey);
    }
}
 