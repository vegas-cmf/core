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
        //resolves actions with http methods
        $actions = $route->getParam('actions');
        if (empty($actions)) {
            $actions = array(
                '/' =>  array(
                    'index' => Method::GET
                )
            );
        }

        //add routes with http method
        foreach ($actions as $actionRoute => $actionMethods) {
            if ($actionRoute == '/') $actionRoute = '';
            foreach ($actionMethods as $action => $method) {
                $newRoute = $router->add($route->getRoute() . $actionRoute,
                    array(
                        'action'    => $action
                    )
                )->via($method);
                $newRoute->setName($route->getName() . '/' . $action);
            }
        }
    }
}