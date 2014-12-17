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

class CustomTask extends \Vegas\Cli\TaskAbstract
{

    public function setupOptions()
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

        $this->addTaskAction(new \Vegas\Cli\Task\Action('testError', 'Test error'));
        $this->addTaskAction(new \Vegas\Cli\Task\Action('testWarning', 'Test warning'));
        $this->addTaskAction(new \Vegas\Cli\Task\Action('testSuccess', 'Test success'));
        $this->addTaskAction(new \Vegas\Cli\Task\Action('testObject', 'Test object'));
        $this->addTaskAction(new \Vegas\Cli\Task\Action('testText', 'Test text'));

        $action = new \Vegas\Cli\Task\Action('testArg', 'Test arguments list');
        $option = new Option('arg', 'a', 'Arg option. Usage app:custom:test 999');
        $action->addOption($option);
        $this->addTaskAction($action);
    }

    public function testAction()
    {
        $this->putText($this->getArg(0));
        $this->putText($this->getOption('f'));
        $this->putObject($this->getArgs());
    }

    public function testErrorAction()
    {
        $this->putError('Error message');
    }

    public function testWarningAction()
    {
        $this->putWarning('Warning message');
    }

    public function testSuccessAction()
    {
        $this->putSuccess('Success message');
    }

    public function testObjectAction()
    {
        $this->putObject(['key' => 'value']);
    }

    public function testTextAction()
    {
        $this->putText('Some text');
    }

    public function testArgAction()
    {
        $this->putText($this->getArg(0));
    }
}