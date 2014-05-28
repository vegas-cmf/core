<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\DI\Service;

use Phalcon\DI;
use Test\Components\Fake;
use Vegas\DI\Service\Component\Renderer;
use Vegas\Mvc\Application;
use Vegas\Mvc\Module\ModuleLoader;
use Vegas\Mvc\View;

class ComponentTest extends \PHPUnit_Framework_TestCase
{
    protected $di;

    public function setUp()
    {
        $di = DI::getDefault();
        $di->set('view', function() use ($di) {
            $view = new View($di->get('config')->application->view->toArray());

            $path = TESTS_ROOT_DIR. '/fixtures/app/module/Test/views';

            if (file_exists($path)) {
                $view->setViewsDir($path);
            }

            return $view;
        });

        $modules = ModuleLoader::dump($di);
        $app = new Application();
        $app->registerModules($modules);

        $this->di = $di;
    }

    public function testRender()
    {
        $component = new Fake();
        $component->setDI($this->di);

        $this->assertInstanceOf('\Phalcon\DI', $component->getDI());

        $rendered = '';
        ob_start();
        $component->render(array('foo' => 'bar', 'baz' => 123));

        $rendered = ob_get_contents();
        ob_clean();

        $this->assertEquals('bar123', $rendered);

        $renderer = new Renderer($this->di->get('view'));
        $component->setRenderer($renderer);

        $rendered = '';
        ob_start();
        $component->render(array('foo' => 'bar', 'baz' => 123));

        $rendered = ob_get_contents();
        ob_clean();

        $this->assertEquals('bar123', $rendered);

        $component = new Fake($renderer);
        $component->setDI($this->di);

        $rendered = '';
        ob_start();
        $component->render(array('foo' => 'bar', 'baz' => 123));

        $rendered = ob_get_contents();
        ob_clean();

        $this->assertEquals('bar123', $rendered);
    }
} 