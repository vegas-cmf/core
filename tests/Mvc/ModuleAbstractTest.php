<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Tests\Mvc;

use Phalcon\DI;
use Vegas\Mvc\Application;
use Vegas\Mvc\Module\ModuleLoader;

class ModuleAbstractTest extends \PHPUnit_Framework_TestCase
{

    public function testModuleAutoloaders()
    {
        $modules = ModuleLoader::dump(DI::getDefault());

        $app = new Application(DI::getDefault());
        $app->registerModules($modules);

        //forms and controller are registered in Module
        $this->assertFalse(class_exists('Test\Controllers\Backend\FakeController'));
        $this->assertFalse(class_exists('Test\Forms\Fake'));

        $this->assertTrue(class_exists('Test\Services\Fake'));
        $this->assertTrue(class_exists('Test\Models\Fake'));
    }

    public function testModuleHandling()
    {
        require_once dirname(__DIR__) . '/fixtures/app/Bootstrap.php';
        $config = require dirname(__DIR__) . '/fixtures/app/config/config.php';
        $config = new \Phalcon\Config($config);
        $bootstrap = new \Bootstrap($config);

        $_SERVER['HTTP_HOST'] = 'vegas.dev';
        $_SERVER['REQUEST_URI'] = '/test/fake/test';
        $bootstrap->setup()->run('/test/fake/test');

        $this->assertTrue(class_exists('Test\Controllers\Backend\FakeController'));
        $this->assertTrue(class_exists('Test\Forms\Fake'));
    }

    public function testPlugins()
    {
        $app = new Application(DI::getDefault());

    }
} 