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

namespace Vegas\Tests\Bootstrap;

class EnvironmentInitializerTraitTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldSetEnvFromConfig()
    {
        $config = [
            'application' => [
                'environment' => \Vegas\Constants::DEV_ENV
            ]
        ];

        $di = \Phalcon\DI::getDefault();

        $trait = $this->getMockForTrait('\Vegas\Bootstrap\EnvironmentInitializerTrait');
        $trait->expects($this->any())
            ->method('getDI')
            ->will($this->returnValue($di));

        $trait->initEnvironment(new \Phalcon\Config($config));

        $this->assertEquals($di->get('environment'), $config['application']['environment']);
    }

    public function testShouldSetDefaultEnv()
    {
        $config = [];

        $di = \Phalcon\DI::getDefault();

        $trait = $this->getMockForTrait('\Vegas\Bootstrap\EnvironmentInitializerTrait');
        $trait->expects($this->any())
            ->method('getDI')
            ->will($this->returnValue($di));

        $trait->initEnvironment(new \Phalcon\Config($config));

        $this->assertEquals($di->get('environment'), \Vegas\Constants::DEFAULT_ENV);
    }

    public function testShouldSetDefineValue()
    {
        $di = \Phalcon\DI::getDefault();

        $trait = $this->getMockForTrait('\Vegas\Bootstrap\EnvironmentInitializerTrait');
        $trait->expects($this->any())
            ->method('getDI')
            ->will($this->returnValue($di));

        $trait->initEnvironment(new \Phalcon\Config([]));

        $this->assertTrue(defined('APPLICATION_ENV'));
    }
}
 