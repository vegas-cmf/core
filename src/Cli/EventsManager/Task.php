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
 
namespace Vegas\Cli\EventsManager;

use Phalcon\CLI\Console;
use Phalcon\CLI\Dispatcher;
use Phalcon\Events\Event;

class Task
{
    public static function beforeHandleTask()
    {
        return function(Event $event, Console $console, Dispatcher $dispatcher) {
            $params = $event->getData()->getParams();
            //parse parameters
            $dispatcher->setParams($params);
        };
    }

    public static function afterHandleTask()
    {
        return function(Event $event, Console $console, \Vegas\Cli\Task $task) {
            echo $task->getOutput();
        };
    }
}