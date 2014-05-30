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
 
namespace Test\Controllers\Frontend;


use Vegas\Mvc\Controller\ControllerAbstract;

/**
 * Class FakeController
 * @package Test\Controllers\Frontend
 * @Auth(name='authUser')
 */
class FakeController extends ControllerAbstract
{
    public function testAction()
    {

    }

    public function jsonAction()
    {
        return $this->jsonResponse(array('foo' => 'bar'));
    }

    public function errorAction($code)
    {
        $throwName = 'throw'.$code;
        $this->$throwName('Message');
    }
} 