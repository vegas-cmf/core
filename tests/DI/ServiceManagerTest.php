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
namespace Vegas\Tests\DI;

use Phalcon\DI;
use Vegas\DI\ServiceManager;

class ServiceManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInjectionAware()
    {
        $di = DI::getDefault();
        $di->set('serviceManager', '\Vegas\DI\ServiceManager');
        
        $sm = new ServiceManager();
        $sm->setDI($di);
        
        $this->assertEquals($di->get('serviceManager'), $sm);
    }

    public function testHasService()
    {
        $di = DI::getDefault();
        $sm = new ServiceManager();
        $sm->setDI($di);

        $this->assertTrue($sm->has('vegas\Tests\Stub:fakeService'));
        $this->assertTrue($sm->hasService('vegas\Tests\Stub:fakeService'));
        $this->assertFalse($sm->has('notExisting:mock'));
        $this->assertFalse($sm->hasService('notExisting:mock'));
    }
    
    public function testGetNotExistingService()
    {
        $di = DI::getDefault();
        $sm = new ServiceManager();
        $sm->setDI($di);
        
        try {
            $sm->getService('notExisting:mock');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\DI\Service\Exception', $ex);
        }
    }
    
    public function testGetService()
    {
        $di = DI::getDefault();
        $sm = new ServiceManager();
        $sm->setDI($di);
        
        $service = $sm->getService('vegas\Tests\Stub:fakeService');
        $this->assertInstanceOf('Vegas\Tests\Stub\Services\FakeService', $service);

        $service = $sm->get('vegas\Tests\Stub:fakeService');
        $this->assertInstanceOf('Vegas\Tests\Stub\Services\FakeService', $service);
    }
}
