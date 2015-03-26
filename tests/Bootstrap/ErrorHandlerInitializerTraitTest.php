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

use Vegas\Test\TestCase;

class ErrorHandlerInitializerTraitTest extends TestCase
{

    public function testInitErrorHandler()
    {
        $config = [
            'application' => [
                'environment' => \Vegas\Constants::DEV_ENV
            ]
        ];

        $di = \Phalcon\DI::getDefault();

        $trait = $this->getMockForTrait('\Vegas\Bootstrap\ErrorHandlerInitializerTrait');
        $trait->expects($this->any())
            ->method('getDI')
            ->will($this->returnValue($di));

        $trait->initErrorHandler(new \Phalcon\Config($config));

        $handler = set_error_handler('var_dump');
        restore_error_handler();

        $this->assertSame($trait, $handler[0]);
        $this->assertEquals('errorHandler', $handler[1]);
    }
}
 