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

use Vegas\Cli\Task\Option;

class CustomTask extends \Vegas\Cli\Task
{

    public function setOptions()
    {
        $action = new \Vegas\Cli\Task\Action('test', 'Test action');

        //foo option
        $foo = new Option('foo', 'f', 'Foo option. Usage app:custom test -f numberOfSth');
        $foo->setValidator(function($value) {
            if (!is_numeric($value)) return false;
            return true;
        });
        $foo->setRequired(true);
        $action->addOption($foo);
        $this->addTaskAction($action);
    }

    public function testAction()
    {
        $this->putText($this->getArg(0));
        $this->putText($this->getOption('f'));
    }
}