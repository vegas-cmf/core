<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\DI\Service;

use Phalcon\DI;
use Test\Components\Fake;
use Vegas\DI\Service\Component\Renderer;
use Vegas\Mvc\Application;
use Vegas\Mvc\Module\Loader as ModuleLoader;
use Vegas\Mvc\View;
use Vegas\Test\TestCase;

class ComponentTest extends TestCase
{
    protected $di;

    public function setUp()
    {
        parent::setUp();

        $di = DI::getDefault();
        $di->set('view', function() use ($di) {
            $view = new View($di->get('config')->application->view->toArray());

            $path = $di->get('config')->application->moduleDir . '/Test/views';

            if (file_exists($path)) {
                $view->setViewsDir($path);
            }

            return $view;
        });
        $modules = (new ModuleLoader($di))->dump(
            $di->get('config')->application->moduleDir,
            $di->get('config')->application->configDir
        );
        $app = new Application();
        $app->registerModules($modules);

        $this->di = $di;
    }

    public function testRender()
    {
        $component = new Fake();
        $component->setDI($this->di);

        $this->assertInstanceOf('\Phalcon\DI', $component->getDI());

        $this->assertEquals('bar123', $component->render(array('foo' => 'bar', 'baz' => 123)));

        $renderer = new Renderer($this->di->get('view'));
        $component->setRenderer($renderer);

        $this->assertEquals('bar123', $component->render(array('foo' => 'bar', 'baz' => 123)));

        $component = new Fake($renderer);
        $component->setDI($this->di);

        $this->assertEquals('bar123', $component->render(array('foo' => 'bar', 'baz' => 123)));
    }
} 