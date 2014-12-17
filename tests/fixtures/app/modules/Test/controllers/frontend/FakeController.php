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
 
namespace Test\Controllers\Frontend;


use Vegas\Mvc\ControllerAbstract;
use Vegas\Mvc\View;

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

    public function testLayoutAction()
    {
        $this->view->disableLevel(View::LEVEL_ACTION_VIEW);
    }

    public function testViewAction()
    {
        $this->view->disableLevel(View::LEVEL_LAYOUT);
    }

    public function testGlobalAction()
    {
        $this->view->disableLevel(View::LEVEL_LAYOUT);
    }

    public function jsonAction()
    {
        return $this->jsonResponse(array('foo' => 'bar'));
    }

    public function emptyjsonAction()
    {
        return $this->jsonResponse();
    }

    public function errorAction($code)
    {
        $throwName = 'throw'.$code;
        $this->$throwName('Message');
    }
} 