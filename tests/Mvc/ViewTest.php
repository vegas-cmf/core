<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */ 

namespace Vegas\Tests\Mvc;

use Vegas\Mvc\View;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function testEngineRegistration()
    {
        $view = new View();

        $this->assertSame(2, count(array_keys($view->getRegisteredEngines())));
        $this->assertArrayHasKey('.volt', $view->getRegisteredEngines());
        $this->assertArrayHasKey('.phtml', $view->getRegisteredEngines());
    }

    public function testViewVars()
    {
        $view = new View();

        $view->testValue = 123;
        $this->assertEquals(123, $view->getVar('testValue'));
        $view->setVar('testValue', 456);
        $this->assertEquals(456, $view->testValue);
    }

    public function testViewOptions()
    {
        $options = array(
            'view' => array(
                'layout'    =>  'main.volt',
                'layoutsDir'    =>  APP_ROOT.'/app/layouts/'
            ),
            'moduleDir' => APP_ROOT.'/app/modules/'
        );
        $view = new View($options);

        $this->assertEquals('main.volt', $view->getLayout());
        $this->assertEquals('../../../../app/layouts/', $view->getLayoutsDir());
    }
}
 