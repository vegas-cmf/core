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

use Vegas\Http\Method;
use Vegas\Mvc\Router\Route;
use Vegas\Mvc\Router\RouteInterface;

class RestRoute implements RouteInterface
{
    public function add(\Phalcon\Mvc\RouterInterface $router, Route $route)
    {
        $via = $route->getParam('via');
        if (empty($via)) {
            $via = array(
                Method::GET, Method::POST, Method::DELETE, Method::HEAD,
                Method::OPTIONS, Method::PATCH, Method::PUT
            );
        }
        $newRoute = $router->add($route->getRoute(), $route->getPaths());
        $newRoute->via($via);
        $newRoute->setName($route->getName());
    }
}