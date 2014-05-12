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

use Phalcon\CLI\Console;
use Vegas\Cli\Exception\TaskNotFoundException;

class Dispatcher
{

    const SEPARATOR = ':';
    const APP_TASK_PREFIX = 'app';
    const CORE_TASK_PREFIX = 'vegas';

    /**
     * @var Console
     */
    private static $consoleApp;

    public static function autoload(Console $console, $arguments)
    {
        $taskName = $arguments[1];
        $taskNameParted = explode(self::SEPARATOR, $taskName);
        if (count($taskNameParted) < 2) {
            throw new TaskNotFoundException();
        }
        self::$consoleApp = $console;

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

        $preparedArguments = array(
            'task'  =>  $taskName,
            'action'    =>  $arguments[2]
        );
        $preparedArguments[] = array_slice($arguments, 3);
        return $preparedArguments;
    }

    /**
     * A function to convert underscore-delimited varnames
     * to CamelCase.  NOTE: it does not leave the first
     * word lowercase
     * @param string $str
     * @throws Exception
     * @return string
     */
    private static function toNamespace($str) {
        $string_parts = preg_split('/_+/', $str);

        if (!is_array($string_parts) || (sizeof($string_parts) < 1)){
            throw new Exception("Unable to split the input string");
        }
        foreach($string_parts as $key => $string_part){
            $string_parts[$key] = ucfirst(strtolower($string_part));
        }
        return implode('\\', $string_parts);
    }

    private static function loadAppTask(array $task)
    {
        if (count($task) > 2) {
            $moduleName = ucfirst($task[1]);
            $taskName = ucfirst($task[2]);
            self::loadAppModuleTask($moduleName);
        } else {
            $taskName = ucfirst($task[1]);
        }

        return $taskName;
    }

    private static function loadAppModuleTask($moduleName)
    {
        $loader = new \Phalcon\Loader();
        $modules = self::$consoleApp->getModules();
        if (!isset($modules[$moduleName])) {
            throw new TaskNotFoundException();
        }
        $module = $moduleName[$moduleName];

        $loader->registerDirs(array(
            $module['path']
        ));
        $loader->register();
    }

    private static function loadCoreTask(array $task)
    {
        $namespace = self::toNamespace($task[1]);
        $taskName = ucfirst($task[2]);

        $fullNamespace = '\Vegas\\' . $namespace . '\\' . $taskName . 'Task';

        $loader = new \Phalcon\Loader();
        $loader->registerNamespaces($fullNamespace);
        $loader->register();

        return $taskName;
    }
}