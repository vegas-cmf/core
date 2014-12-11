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
 
namespace Vegas\Tests\DI;

use Phalcon\DI;
use Vegas\Constants;
use Vegas\DI\ServiceProviderLoader;

class ServiceProviderLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testDump()
    {
        $serviceProviderLoader = new ServiceProviderLoader(DI::getDefault());
        $serviceProviderLoader->dump(
            TESTS_ROOT_DIR . '/fixtures/app/services/',
            TESTS_ROOT_DIR . '/fixtures/app/config/'
        );
        $this->assertTrue(file_exists(TESTS_ROOT_DIR . '/fixtures/app/config/services.php'));
    }

    public function testAutoload()
    {
        $di = DI::getDefault();
        $di->set('environment', function() {
            return Constants::DEV_ENV;
        }, true);
        $serviceProviderLoader = new ServiceProviderLoader(DI::getDefault());
        $serviceProviderLoader->autoload(
            TESTS_ROOT_DIR . '/fixtures/app/services/',
            TESTS_ROOT_DIR . '/fixtures/app/config/'
        );

        $this->assertTrue($di->has('url'));
        $this->assertTrue($di->has('assets'));
        $this->assertFalse($di->has('acl'));
    }
} 