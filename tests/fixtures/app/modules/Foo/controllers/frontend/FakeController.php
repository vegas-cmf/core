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
 
namespace Foo\Controllers\Frontend;

use Vegas\Mvc\ControllerAbstract;

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