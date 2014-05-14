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
 
namespace Vegas\Tests\DI;

use Phalcon\DI;
use Vegas\DI\ServiceProviderLoader;

class ServiceProviderLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testDump()
    {
        ServiceProviderLoader::dump(DI::getDefault());
        $this->assertTrue(file_exists(TESTS_ROOT_DIR . '/fixtures/app/config/services.php'));
    }

    public function testAutoload()
    {
        $di = DI::getDefault();
        $di->set('environment', function() {
            return 'development';
        }, true);
        ServiceProviderLoader::autoload($di);

        $this->assertTrue($di->has('url'));
        $this->assertTrue($di->has('assets'));
        $this->assertFalse($di->has('acl'));
    }
} 