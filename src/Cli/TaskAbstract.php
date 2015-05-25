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
 
namespace Vegas\Cli;

use Vegas\Cli\Task\Action;
use Vegas\Cli\Task\Exception\InvalidArgumentException;
use Vegas\Cli\Task\Exception\InvalidOptionException;
use Vegas\Cli\Task\Exception\MissingRequiredArgumentException;

/**
 * Class Task
 * @package Vegas\Cli
 */
abstract class TaskAbstract extends \Phalcon\CLI\Task
{
    const HELP_OPTION = 'help';
    const HELP_SHORTOPTION = 'h';

    /**
     * Trait for coloring console output
     */
    use ColorsOutputTrait;

    /**
     * Task's available options
     *
     * @return mixed
     */
    abstract public function setupOptions();

    /**
     * Task output buffer
     *
     * @var array
     * @internal
     */
    private $outputBuffer = [];

    /**
     * Available task actions
     *
     * @var array
     * @internal
     */
    private $actions = [];

    /**
     * Action which is going to be executed
     *
     * @var string
     * @internal
     */
    private $actionName;

    /**
     * Active task name
     *
     * @var string
     * @internal
     */
    private $taskName;

    /**
     * Array of execution arguments
     *
     * @var array
     * @internal
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

        $this->setupOptions();

        //if -h or --help option was typed in command line then show only help
        $this->args = $this->dispatcher->getParam('args');
        if ($this->containHelpOption($this->args)) {
            $this->renderActionHelp();
            //stop dispatching
            return false;
        }
        try {
            $this->validate($this->args);
        } catch (InvalidArgumentException $ex) {
            $this->throwError(strtr(':command: Invalid argument `:argument` for option `:option`', [
                ':command' => sprintf('%s %s', $this->dispatcher->getParam('activeTask'), $this->dispatcher->getParam('activeAction')),
                ':option' => $ex->getOption(),
                ':argument' => $ex->getArgument()
            ]));
        } catch (InvalidOptionException $ex) {
            $this->throwError(strtr(':command: Invalid option `:option`', [
                ':command' => sprintf('%s %s', $this->dispatcher->getParam('activeTask'), $this->dispatcher->getParam('activeAction')),
                ':option' => $ex->getOption()
            ]));
        }

        return true;
    }

    /**
     * Action executed by default when no action was specified in command line
     */
    public function mainAction()
    {
        $this->renderTaskHelp();
    }

    /**
     * Determines if help option was typed in command line
     *
     * @param $args
     * @return bool
     */
    private function containHelpOption($args)
    {
        return array_key_exists(self::HELP_OPTION, $args)
                || array_key_exists(self::HELP_SHORTOPTION, $args);
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
     * Returns array of execution arguments
     *
     * @return array
     */
    protected function getArgs()
    {
        return $this->args;
    }

    /**
     * Return argument from indicated index
     *
     * @param $index
     * @return null|mixed
     */
    protected function getArg($index)
    {
        $arg = null;
        if (isset($this->args[$index])) {
            $arg = $this->args[$index];
        }

        return $arg;
    }

    /**
     * Get option value for action from command line
     *
     * @param $name
     * @param null $default
     * @throws MissingRequiredArgumentException
     * @return mixed
     */
    protected function getOption($name, $default = null)
    {
        $matchedOption = null;
        foreach ($this->actions[$this->actionName]->getOptions() as $option) {
            if ($option->matchParam($name)) {
                $matchedOption = $option;
            }
        }
        $value = $matchedOption->getValue($this->args, $default);

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
     * Clears collected output buffer
     *
     * @return $this
     */
    public function clearOutput()
    {
        $this->outputBuffer = [];
        return $this;
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
     * Puts warning message into buffer
     *
     * @param $str
     */
    public function putWarning($str)
    {
        $this->appendLine($this->getColoredString($str, 'yellow'));
    }

    /**
     * Puts success message into buffer
     *
     * @param $str
     */
    public function putSuccess($str)
    {
        $this->appendLine($this->getColoredString($str, 'green'));
    }

    /**
     * Puts error message into buffer
     *
     * @param $str
     */
    public function putError($str)
    {
        $this->appendLine($this->getColoredString($str, 'red'));
    }

    /**
     * Dumps object into buffer
     *
     * @param $object
     */
    public function putObject($object)
    {
        $this->appendLine($this->getColoredString(print_r($object, true), 'black', 'light_gray'));
    }

    /**
     * Throws CLI error
     *
     * @param $str
     * @throws \Exception
     */
    public function throwError($str)
    {
        throw new \Exception($this->getColoredString($str, 'red', 'dark_gray'));
    }

    /**
     *  Renders help for task action
     */
    protected function renderActionHelp()
    {
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

    /**
     *  Renders help for task
     */
    protected function renderTaskHelp()
    {
        $this->appendLine($this->getColoredString('Available actions', 'dark_gray'));
        $this->appendLine(PHP_EOL);
        foreach ($this->actions as $action) {
            $this->appendLine(sprintf(
                '   %s      %s',
                $this->getColoredString($action->getName(), 'light_green'),
                $this->getColoredString($action->getDescription(), 'green'))
            );
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
