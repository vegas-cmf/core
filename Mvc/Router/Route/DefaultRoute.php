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
 
namespace Vegas\Mvc\Router\Route;


use Vegas\Mvc\Router\Route;
use Vegas\Mvc\Router\RouteInterface;

class DefaultRoute implements RouteInterface
{
    public function add(\Phalcon\Mvc\RouterInterface $router, Route $route)
    {
        $router
            ->add($route->getRoute(), $route->getPaths())
            ->setName($route->getName());
    }
} 