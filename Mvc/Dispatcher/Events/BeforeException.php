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
namespace Vegas\Mvc\Dispatcher\Events;

use Phalcon\Dispatcher,
    Phalcon\Events\Event,
    Vegas\Mvc\Dispatcher\ExceptionResolver;

class BeforeException
{
    public static function fire()
    {
        return function(Event $event, Dispatcher $dispatcher, \Exception $exception) {
            $resolver = new ExceptionResolver();
            $resolver->setDI($dispatcher->getDI());
            $resolver->resolve($exception);
            
            return false;
        };
    }
} 