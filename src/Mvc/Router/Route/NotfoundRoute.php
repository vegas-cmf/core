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


use Vegas\Mvc\Router\Route;
use Vegas\Mvc\Router\RouteInterface;

/**
 * Class NotfoundRoute
 *
 * When none of the routes specified in the router are matched, the following route will be use
 *
 * @package Vegas\Mvc\Router\Route
 */
class NotfoundRoute implements RouteInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(\Phalcon\Mvc\RouterInterface $router, Route $route)
    {
        $router
            ->notFound($route->getPaths());
    }
} 