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

/**
 * Class RestRoute
 * REST route type, useful for building RESTful API
 *
 * @package Vegas\Mvc\Router\Route
 */
class RestRoute implements RouteInterface
{
    /**
     * {@inheritdoc}
     */
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
        //for each action new route is adding with specified HTTP Method.
        foreach ($actions as $actionRoute => $actionMethods) {
            if ($actionRoute == '/') $actionRoute = '';
            foreach ($actionMethods as $action => $method) {
                $paths = $route->getPaths();
                $paths['action'] = $action;

                $newRoute = $router->add($route->getRoute() . $actionRoute, $paths)->via($method);
                $newRoute->setName($route->getName() . '/' . $action);
            }
        }
    }
}