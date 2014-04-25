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

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testModuleRegister()
    {
        $modules = ModuleLoader::dump(DI::getDefault());
        $app = new Application();
        $app->registerModules($modules);

        $this->assertSameSize($modules, $app->getModules());

        $this->assertTrue(class_exists('Test\\Models\\Fake'));
        $this->assertTrue(class_exists('Test\\Components\\Fake'));
        $this->assertTrue(class_exists('Test\\Services\\Fake'));
    }

} 