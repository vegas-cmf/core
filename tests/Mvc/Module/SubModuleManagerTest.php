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
 
namespace Vegas\Tests\Mvc\Module;


use Vegas\Mvc\Module\SubModuleManager;

class SubModuleManagerTest  extends \PHPUnit_Framework_TestCase
{
    public function testRegisterSubModule()
    {
        $manager = new SubModuleManager();
        $manager->registerSubModule('frontend');
        $manager->registerSubModule('backend');
        $manager->registerSubModule('custom');

        $this->assertTrue(SubModuleManager::isRegistered('frontend'));
        $this->assertTrue(SubModuleManager::isRegistered('backend'));
        $this->assertTrue(SubModuleManager::isRegistered('custom'));
    }
} 