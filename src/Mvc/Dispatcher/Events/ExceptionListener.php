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
namespace Vegas\Mvc\Dispatcher\Events;

use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Vegas\Mvc\Dispatcher\ExceptionResolver;

/**
 * Class BeforeException
 * @package Vegas\Mvc\Dispatcher\Events
 */
class ExceptionListener
{
    /**
     * Event fired when exception is throwing
     *
     */
    public function beforeException() {
        /**
         * @param \Phalcon\Events\Event $event
         * @param \Phalcon\Dispatcher $dispatcher
         * @param \Exception $exception
         * @return callable
         */
        return function(Event $event, Dispatcher $dispatcher, \Exception $exception) {
            $resolver = new ExceptionResolver();
            $resolver->setDI($dispatcher->getDI());
            $resolver->resolve($exception);

            return false;
        };
    }
} 