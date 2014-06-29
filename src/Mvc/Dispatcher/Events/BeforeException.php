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
namespace Vegas\Mvc\Dispatcher\Events;

use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Vegas\Mvc\Dispatcher\ExceptionResolver;

/**
 * Class BeforeException
 * @package Vegas\Mvc\Dispatcher\Events
 */
class BeforeException
{
    /**
     * @return callable
     */
    public static function getEvent()
    {
        return function(Event $event, Dispatcher $dispatcher, \Exception $exception) {
            $resolver = new ExceptionResolver();
            $resolver->setDI($dispatcher->getDI());
            $resolver->resolve($exception);
            
            return false;
        };
    }
} 