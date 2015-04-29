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
 * Class FooController
 * @package Test\Controllers\Frontend
 * @Auth(name='authUser')
 */
class FooController extends ControllerAbstract
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

    public function testLocalAction()
    {
        $this->view->disableLevel(View::LEVEL_LAYOUT);
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

    public function translateAction($str)
    {
        /**
         * @TODO rollback to previous version when https://github.com/phalcon/cphalcon/pull/10088 will be added to release
         */
        return $this->response->setContent('test');

        $this->view->disable();
        return $this->response->setContent(
            $this->_($str)
        );
    }
}
