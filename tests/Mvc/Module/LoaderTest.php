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
 
namespace Vegas\Tests\Mvc\Module;

use Phalcon\Di;
use Vegas\Mvc\Module\Loader as ModuleLoader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldLoadAllModulesFromApplication()
    {
        $moduleLoader = new ModuleLoader(Di::getDefault());
        $modules = $moduleLoader->dump(
            TESTS_ROOT_DIR . '/fixtures/app/modules/',
            TESTS_ROOT_DIR . '/fixtures/app/config/'
        );

        $this->assertInternalType('array', $modules);

        $this->assertTrue(file_exists(TESTS_ROOT_DIR . '/fixtures/app/config/modules.php'));
        $modulesArray = require TESTS_ROOT_DIR . '/fixtures/app/config/modules.php';

        $this->assertInternalType('array', $modulesArray);
        $this->assertInternalType('string', ModuleLoader::MODULE_SETTINGS_FILE);

        $this->assertArrayHasKey('Foo', $modulesArray);
        $this->assertArrayHasKey('FakeVendorModule', $modulesArray);
    }

} 