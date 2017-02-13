<?php
/**
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright (c) 2015, Amsterdam Standard
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

        $di = \Phalcon\Di::getDefault();

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
 