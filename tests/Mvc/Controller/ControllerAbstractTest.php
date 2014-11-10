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
namespace Vegas\Tests\Mvc\Controller;

use Vegas\Tests\App\TestCase;

class ControllerAbstractTest extends TestCase
{
    public function testNoActionError()
    {
        $this->bootstrap->run('/test/front/no-action-like-this');
    }

    public function testNoActionErrorResponse()
    {
        $this->assertEquals(
            '404 Action \'no-action-like-this\' was not found on handler \'Frontend\Fake\'',
            $this->di->get('response')->getHeaders()->get('Status')
        );
    }

    public function test403Error()
    {
        $this->bootstrap->run('/test/front/error/403');
    }

    public function test403ErrorResponse()
    {
        $this->assertEquals('403 Message', $this->di->get('response')->getHeaders()->get('Status'));
    }

    public function test404Error()
    {
        $this->bootstrap->run('/test/front/error/404');
    }

    public function test404ErrorResponse()
    {
        $this->assertEquals('404 Message', $this->di->get('response')->getHeaders()->get('Status'));
    }

    public function test500Error()
    {
        $this->bootstrap->run('/test/front/error/500');
    }

    public function test500ErrorResponse()
    {
        $this->assertEquals('500 Message', $this->di->get('response')->getHeaders()->get('Status'));
    }

    public function testJson()
    {
        $this->assertEquals('{"foo":"bar"}', $this->bootstrap->run('/test/front/json'));
    }

    public function testEmptyJson()
    {
        $this->assertEquals('[]', $this->bootstrap->run('/test/front/emptyjson'));
    }
}