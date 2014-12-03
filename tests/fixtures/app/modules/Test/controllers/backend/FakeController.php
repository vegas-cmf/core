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
 
namespace Test\Controllers\Backend;

use Vegas\Mvc\ControllerAbstract;
use Vegas\Mvc\View;

class FakeController extends ControllerAbstract
{
    public function testAction()
    {
        $this->view->disableLevel(View::LEVEL_LAYOUT);
    }
} 