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
    /**
     * @expectedException \Phalcon\Mvc\Dispatcher\Exception
     * @expectedExceptionMessage Action 'no-action-like-this' was not found on handler 'Frontend\Fake'
     */
    public function testNoActionErrorResponse()
    {
        try {
            $this->handleUri('/test/front/no-action-like-this');
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * @expectedException \Vegas\Exception
     * @expectedExceptionCode 403
     */
    public function test403ErrorResponse()
    {
        try {
            $this->handleUri('/test/front/error/403');
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * @expectedException \Vegas\Exception
     * @expectedExceptionCode 404
     */
    public function test404ErrorResponse()
    {
        try {
            $this->handleUri('/test/front/error/404');
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * @expectedException \Vegas\Exception
     * @expectedExceptionCode 500
     */
    public function test500ErrorResponse()
    {
        try {
            $this->handleUri('/test/front/error/500');
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
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