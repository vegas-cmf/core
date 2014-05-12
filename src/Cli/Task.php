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
 
namespace Vegas\Cli;

use Vegas\Cli\Task\Action;

abstract class Task extends \Phalcon\CLI\Task
{
    abstract public function getOptions();

    private $outputBuffer = array();

    private $actions = array();

    public function initialize()
    {
    }

    protected function addAction(Action $action)
    {
        $this->actions[$action->getName()] = $action;
    }

    public function getOutput()
    {
        return implode(PHP_EOL, $this->outputBuffer);
    }

    public function putText($str)
    {
        $this->outputBuffer[] = $str;
    }

    public function pubObject($object)
    {
        $this->outputBuffer[] = print_r($object, true);
    }

    protected function renderHelp()
    {
        $actionName = $this->dispatcher->getActionName();

    }

    protected function validate($params)
    {
        $actionName = $this->dispatcher->getActionName();
        if (isset($this->actions[$actionName])) {
            foreach ($params as $param => $value) {

            }
//            $this->actions[$actionName]->validate($params);
        }
    }
}