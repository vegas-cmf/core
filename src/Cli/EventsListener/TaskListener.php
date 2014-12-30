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
 
namespace Vegas\Cli\EventsListener;

use Phalcon\CLI\Console;
use Phalcon\CLI\Dispatcher;
use Phalcon\Events\Event;
use Vegas\Cli\OptionParser;
use Vegas\Cli\Task;
use Vegas\Cli\TaskAbstract;

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
    public function beforeHandleTask($argv)
    {
        return function(Event $event, Console $console, Dispatcher $dispatcher) use ($argv) {
            //parse parameters
            $parsedOptions = OptionParser::parse($argv);

            $dispatcher->setParams(array(
                'activeTask'  => isset($parsedOptions[0]) ? $parsedOptions[0] : false,
                'activeAction'  => isset($parsedOptions[1]) ? $parsedOptions[1] : false,
                'args'    =>  count($parsedOptions) > 2 ? array_slice($parsedOptions, 2) : []
            ));
        };
    }

    /**
     * Event fired after task handle
     *
     * @return callable
     */
    public function afterHandleTask()
    {
        return function(Event $event, Console $console, TaskAbstract $task) {
            echo $task->getOutput();
        };
    }
}
