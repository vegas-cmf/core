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
use Vegas\Mvc\Module\ModuleLoader;

class ModuleLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testDump()
    {
        ModuleLoader::dump(DI::getDefault());
        $this->assertTrue(file_exists(TESTS_ROOT_DIR . '/fixtures/app/config/modules.php'));
    }

} 