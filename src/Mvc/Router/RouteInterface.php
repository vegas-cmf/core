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
 
namespace Vegas\Mvc\Router;

use Vegas\Mvc\Router\Route;

/**
 * Interface RouteInterface
 * @package Vegas\Mvc\Router
 * @codeCoverageIgnore
 */
interface RouteInterface
{
    /**
     * Methods adds new Route definition to indicated Router
     *
     * @param \Phalcon\Mvc\RouterInterface $router
     * @param Route $route
     * @return mixed
     */
    public function add(\Phalcon\Mvc\RouterInterface $router, Route $route);
}