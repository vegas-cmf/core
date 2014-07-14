<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Cli\EventsListener;

use Phalcon\CLI\Console;
use Phalcon\CLI\Dispatcher;
use Phalcon\Events\Event;
use Vegas\Cli\OptionParser;

/**
 * Class TaskListener
 * @package Vegas\Cli\EventsManager
 */
class TaskListener
{
    /**
     * Event fired before task is handling
     *
     * @param $argv
     * @return callable
     */
    public static function beforeHandleTask($argv)
    {
        return function(Event $event, Console $console, Dispatcher $dispatcher) use ($argv) {
            //parse parameters
            $parsedOptions = OptionParser::parse($argv);

            $dispatcher->setParams(array(
                'activeTask'  => $parsedOptions[0],
                'activeAction'  => $parsedOptions[1],
                'args'    =>  array_slice($parsedOptions, 2)
            ));
        };
    }

    /**
     * Event fired after task handle
     *
     * @return callable
     */
    public static function afterHandleTask()
    {
        return function(Event $event, Console $console, \Vegas\Cli\Task $task) {
            echo $task->getOutput();
        };
    }
}