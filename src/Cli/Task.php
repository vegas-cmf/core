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
use Vegas\Cli\Task\Exception\InvalidArgumentException;
use Vegas\Cli\Task\Exception\InvalidOptionException;
use Vegas\Cli\Task\Exception\MissingArgumentException;
use Vegas\Cli\Task\Option;

/**
 * Class Task
 * @package Vegas\Cli
 */
abstract class Task extends \Phalcon\CLI\Task
{
    const HELP_OPTION = 'help';
    const HELP_SHORTOPTION = 'h';

    /**
     * Trait for coloring console output
     */
    use ColorsOuputTrait;

    /**
     * Task must implement this method to set available options
     *
     * @return mixed
     */
    abstract public function setOptions();

    /**
     * Task output buffer
     *
     * @var array
     */
    private $outputBuffer = array();

    /**
     * Available task actions
     *
     * @var array
     */
    private $actions = array();

    /**
     * Action which is going to be executed
     *
     * @var string
     */
    private $actionName;

    /**
     * Active task name
     *
     * @var string
     */
    private $taskName;

    /**
     * @var array
     */
    private $args;

    /**
     * Sets available actions with options
     * Validates command line arguments and options for command
     * Stops dispatching when detects help option
     *
     * @see http://docs.phalconphp.com/pl/latest/reference/dispatching.html
     * @return bool
     */
    public function beforeExecuteRoute()
    {
        //sets active task and action names
        $this->actionName = $this->dispatcher->getActionName();
        $this->taskName = $this->dispatcher->getTaskName();

        $this->setOptions();

        //if -h or --help option was typed in command line then show only help
        $this->args = $this->dispatcher->getParam('args');
        if ($this->containHelpOption($this->args)) {
            $this->renderHelp();
            //stop dispatching
            return false;
        }
        try {
            $this->validate($this->args);
        } catch (InvalidArgumentException $ex) {
            $this->putError(strtr(':command: Invalid argument `:argument` for option `:option`', array(
                ':command' => sprintf('%s %s', $this->dispatcher->getParam('activeTask'), $this->dispatcher->getParam('activeAction')),
                ':option' => $ex->getOption(),
                ':argument' => $ex->getArgument()
            )));

            $this->renderHelp();
        } catch (InvalidOptionException $ex) {
            $this->putError(strtr(':command: Invalid option `:option`', array(
                ':command' => sprintf('%s %s', $this->dispatcher->getParam('activeTask'), $this->dispatcher->getParam('activeAction')),
                ':option' => $ex->getOption()
            )));

            $this->renderHelp();
        }
    }

    /**
     * Determines if help option was typed in command line
     *
     * @param $args
     * @return bool
     */
    private function containHelpOption($args)
    {
        return array_search(self::HELP_OPTION, $args) || array_search(self::HELP_SHORTOPTION, $args);
    }

    /**
     * Appends new line to buffer
     *
     * @param $str
     * @return $this
     */
    private function appendLine($str)
    {
        $this->outputBuffer[] = $str . PHP_EOL;
        return $this;
    }

    /**
     * @return array
     */
    protected function getArgs()
    {
        return $this->args;
    }

    /**
     * @param $name
     * @return null
     */
    protected function getArg($name)
    {
        $arg = null;
        if (isset($this->args[$name])) {
            $arg = $this->args[$name];
        }

        return $arg;
    }

    /**
     * Get option value for action from command line
     *
     * @param $name
     * @return mixed
     * @throws
     */
    protected function getOption($name, $default = null)
    {
        $matchedOption = null;
        foreach ($this->actions[$this->actionName]->getOptions() as $option) {
            if ($option->matchParam($name)) {
                $matchedOption = $option;
            }
        }
        if ($matchedOption instanceof Option) {
            $value = $matchedOption->getValue($this->args, $default);

            if ($matchedOption->isRequired() && empty($value)) {
                throw new MissingArgumentException($name);
            }
        } else {
            throw new MissingArgumentException($name);
        }

        return $value;
    }

    /**
     * Adds available action for current Task
     *
     * @param Action $action
     * @return $this
     */
    final protected function addTaskAction(Action $action)
    {
        $this->actions[$action->getName()] = $action;
        return $this;
    }

    /**
     * Returns collected output
     *
     * @return string
     */
    public function getOutput()
    {
        return implode('', $this->outputBuffer);
    }

    /**
     * Puts text into buffer
     *
     * @param $str
     */
    public function putText($str)
    {
        $this->appendLine($this->getColoredString($str, 'light_blue'));
    }

    /**
     * Puts error into console
     *
     * @param $str
     * @throws \Exception
     */
    public function putError($str)
    {
        throw new \Exception($this->getColoredString($str, 'red', 'dark_gray'));
    }

    /**
     * Dumps object into buffer
     *
     * @param $object
     */
    public function pubObject($object)
    {
        $this->appendLine($this->getColoredString(print_r($object, true), 'black', 'light_gray'));
    }

    /**
     *  Renders help for command
     */
    protected function renderHelp()
    {
        if (!isset($this->actions[$this->actionName])) {
            $this->appendLine('No help available');
        } else {
            $action = $this->actions[$this->actionName];
            //puts name of action
            $this->appendLine($this->getColoredString($action->getDescription(), 'green'));
            $this->appendLine('');

            //puts usage hint
            $this->appendLine('Usage:');
            $this->appendLine($this->getColoredString(sprintf(
                '   %s %s [options]',
                $this->dispatcher->getParam('activeTask'),
                $this->dispatcher->getParam('activeAction')
            ), 'dark_gray'));
            $this->appendLine('');

            //puts available options
            $this->appendLine($this->getColoredString('Options:', 'gray'));
            foreach ($action->getOptions() as $option) {
                $this->appendLine($this->getColoredString(sprintf(
                    '   --%s     -%s      %s',
                    $option->getName(), $option->getShortName(), $option->getDescription()
                ), 'light_green'));
            }
        }
    }

    /**
     * Validates action options
     */
    protected function validate()
    {
        $args = $this->dispatcher->getParam('args');
        if (isset($this->actions[$this->actionName])) {
            $action = $this->actions[$this->actionName];
            $action->validate($args);
        }
    }
}