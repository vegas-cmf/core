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
namespace Vegas\Tests\Mvc;

use Vegas\Test\TestCase;

class ControllerAbstractTest extends TestCase
{
    public function testNoActionErrorResponse()
    {
        $response = $this->handleUri('/test/front/no-action-like-this');
        $this->assertEquals(
            '404 Action \'no-action-like-this\' was not found on handler \'Frontend\Fake\'',
            $response->getHeaders()->get('Status')
        );
    }

    public function test403ErrorResponse()
    {
        $response = $this->handleUri('/test/front/error/403');
        $this->assertEquals('403 Message', $response->getHeaders()->get('Status'));
    }

    public function test404ErrorResponse()
    {
        $response = $this->handleUri('/test/front/error/404');
        $this->assertEquals('404 Message', $response->getHeaders()->get('Status'));
    }

    public function test500ErrorResponse()
    {
        $response = $this->handleUri('/test/front/error/500');
        $this->assertEquals('500 Message', $response->getHeaders()->get('Status'));
    }

    public function testJson()
    {
        $this->assertEquals('{"foo":"bar"}', $this->bootstrap->run('/test/front/json'));
    }

    public function testEmptyJson()
    {
        $this->assertEquals('[]', $this->bootstrap->run('/test/front/emptyjson'));
    }

    public function testTranslatorAlias()
    {
        $this->assertEquals('test', $this->bootstrap->run('/testfoo/front/translate/test'));
    }
}