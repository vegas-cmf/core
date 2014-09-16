<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Mvc\View\Engine;

use Phalcon\DI;
use Vegas\Mvc\View;
use Vegas\Tests\App\TestCase;

class VoltTest extends TestCase
{
    public function testHelpers()
    {
        $view = new View();

        $engines = $view->getRegisteredEngines();

        $volt = $engines['.volt'];
        $volt = $volt($view, DI::getDefault());

        $compiler = $volt->getCompiler();

        $this->assertEquals('<?php echo (new \Vegas\Tag\ShortenText())->prepare(\'foo\',100, "..."); ?>', $compiler->compileString('{{ shortenText("foo") }}'));
        $this->assertEquals('<?php echo (new \Vegas\Tag\ShortenText())->prepare(\'foo\',50, \'bar\'); ?>', $compiler->compileString('{{ shortenText("foo", 50, "bar") }}'));

        $this->assertEquals('<?php echo (new \Vegas\Tag\Pagination($this->getDI()))->render($page,array()); ?>', $compiler->compileString('{{ pagination(page) }}'));
        $this->assertEquals('<?php echo (new \Vegas\Tag\Pagination($this->getDI()))->render($page,array(\'class\' => \'test\')); ?>', $compiler->compileString('{{ pagination(page,["class": "test"]) }}'));
    }

    public function testPartial()
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
            $output = $this->bootstrap->run($url);
            return $output;
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

        //extract volt engine
        $view = $this->bootstrap->getDI()->get('view');
        $engines = $view->getRegisteredEngines();
        $volt = $engines['.volt']($view, $this->bootstrap->getDI());

        ob_start();
        $volt->partial('test/sample');
        $this->assertEquals('2', ob_get_contents());
        ob_end_clean();
    }
}