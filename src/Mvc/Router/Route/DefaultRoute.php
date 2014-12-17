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
 
namespace Vegas\Mvc\Router\Route;


use Phalcon\Mvc\RouterInterface;
use Vegas\Mvc\Router\Route;
use Vegas\Mvc\Router\RouteInterface;

/**
 * Class DefaultRoute
 * Default route type.
 * @see http://docs.phalconphp.com/pl/latest/reference/routing.html#setting-default-paths
 *
 * @package Vegas\Mvc\Router\Route
 */
class DefaultRoute implements RouteInterface
{

    /**
     * {@inheritdoc}
     */
    public function add(RouterInterface $router, Route $route)
    {
        $router->setDefaults(array_merge(
            $route->getPaths(),
            [
                'params' => $route->getParams()
            ]
        ));
    }
} 