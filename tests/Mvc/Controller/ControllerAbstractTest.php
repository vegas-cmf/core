<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Mvc\Controller;

use Vegas\Tests\App\TestCase;

class ControllerAbstractTest extends TestCase
{
    /**
     * @TODO fill this
     */
    public function testErrors()
    {
        $this->bootstrap->run('/test/front/error/403');
        $this->bootstrap->run('/test/front/error/404');
        $this->bootstrap->run('/test/front/error/500');
    }

    /**
     * @TODO fill this
     */
    public function testJson()
    {
        $this->bootstrap->run('/test/front/json');
    }
}