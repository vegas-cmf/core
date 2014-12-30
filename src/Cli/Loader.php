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

use Vegas\Cli\Exception as CliException;
use Vegas\Cli\Exception\TaskNotFoundException;

/**
 * Class Loader
 *
 * Parses command line arguments passed to CLI application and loads indicated tasks
 *
 * @package Vegas\Cli
 */
class Loader
{
    const SEPARATOR = ':';
    const APP_TASK_PREFIX = 'app';
    const CORE_TASK_PREFIX = 'vegas';

    /**
     * @var Console
     * @internal
     */
    private $consoleApp;

    /**
     * Parses indicated arguments from command line
     * Returns prepared array with task, action and additional arguments
     *
     * @param Console $console
     * @param $arguments
     * @throws Exception\TaskActionNotSpecifiedException
     * @throws Exception\TaskNotFoundException
     * @return array
     */
    public function parseArguments(Console $console, $arguments)
    {
        $this->consoleApp = $console;
        if (count($arguments) == 1) {
            throw new TaskNotFoundException();
        }
        $taskName = $this->lookupTaskClass($arguments);

        //prepares an array containing arguments for CLI handler
        $parsedArguments = array(
            'task'  =>  $taskName,
            'action'    =>  isset($arguments[2]) ? $arguments[2] : false
        );
        //adds additional arguments
        $parsedArguments[] = count($arguments) > 3 ? array_slice($arguments, 3) : array();
        return $parsedArguments;
    }

    /**
     * Resolves name of task class
     * The task classes placed in the following location will be resolved
     *      * php cli.php app:example ...       ->  app/tasks/ExampleTask.php
     *      * php cli.php app:foo:example ...   ->  app/modules/Foo/tasks/ExampleTask.php
     *      * php cli.php vegas:cli:example ... -> (vendor path)/vegas-cmf/core/Cli/Task/ExampleTask.php
     *
     * @param $arguments
     * @return string
     * @throws Exception\TaskNotFoundException
     */
    protected function lookupTaskClass($arguments)
    {
        $taskName = $arguments[1];
        $taskNameParted = explode(self::SEPARATOR, $taskName);
        if (count($taskNameParted) < 2) {
            throw new TaskNotFoundException();
        }

        switch ($taskNameParted[0]) {
            case self::APP_TASK_PREFIX:
                $taskName = self::loadAppTask($taskNameParted);
                break;
            case self::CORE_TASK_PREFIX:
                $taskName = self::loadCoreTask($taskNameParted);
                break;
            default:
                throw new TaskNotFoundException();
        }

        return $taskName;
    }

    /**
     * Converts indicated string to namespace format.
     *
     * @param string $str
     * @throws CliException
     * @return string
     * @internal
     */
    private function toNamespace($str) {
        $stringParts = preg_split('/_+/', $str);

        foreach($stringParts as $key => $stringPart){
            $stringParts[$key] = ucfirst(strtolower($stringPart));
        }
        return implode('\\', $stringParts) . '\\';
    }

    /**
     * @param $namespace
     * @internal
     */
    private function registerClass($namespace)
    {
        //registers task class in Class Loader
        $reflectionClass = new \ReflectionClass($namespace);
        $loader = new \Phalcon\Loader();
        $loader->registerClasses(array(
            $namespace => $reflectionClass->getFileName()
        ), true);
        $loader->register();
    }

    /**
     * Loads task from application directory
     *
     * @param array $task
     * @return string
     * @internal
     */
    private function loadAppTask(array $task)
    {
        //if task name contains more than 2 parts then it comes from module
        if (count($task) > 2) {
            $moduleName = ucfirst($task[1]);
            $taskName = ucfirst($task[2]);
            $taskName = $this->loadAppModuleTask($moduleName, $taskName);
        } else {
            $taskName = ucfirst($task[1]);
        }

        return $taskName;
    }

    /**
     * Loads task from specified application module
     *
     * @param $moduleName
     * @param $taskName
     * @return string
     * @throws Exception\TaskNotFoundException
     * @internal
     */
    private function loadAppModuleTask($moduleName, $taskName)
    {
        $modules = $this->consoleApp->getModules();
        //checks if indicated module has been registered
        if (!isset($modules[$moduleName])) {
            throw new TaskNotFoundException();
        }

        //creates full namespace for task class placed in application module
        $fullNamespace = strtr('\:moduleName\Tasks\:taskName', array(
            ':moduleName' => $moduleName,
            ':taskName' => $taskName
        ));

        //registers task class in Class Loader
        $this->registerClass($fullNamespace . 'Task');

        //returns converted name of task  (namespace of class containing task)
        return $fullNamespace;
    }

    /**
     * Loads task from Vegas libraries, mostly placed in vendor
     *
     * @param array $task
     * @return string
     * @internal
     */
    private function loadCoreTask(array $task)
    {
        //creates full namespace for task placed in Vegas library
        if (count($task) == 3) {
            $namespace = $this->toNamespace($task[1]);
            $taskName = ucfirst($task[2]);
        } else {
            //for \Vegas namespace tasks are placed in \Vegas\Task namespace
            $namespace = '';
            $taskName = ucfirst($task[1]);
        }
        $fullNamespace = strtr('\Vegas\:namespaceTask\\:taskName', array(
            ':namespace' => $namespace,
            ':taskName' =>  $taskName
        ));

        //registers task class in Class Loader
        $this->registerClass($fullNamespace . 'Task');

        //returns converted name of task (namespace of class containing task)
        return $fullNamespace;
    }
}
