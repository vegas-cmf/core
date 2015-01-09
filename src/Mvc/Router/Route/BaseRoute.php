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
 * Base route type
 *
 * @package Vegas\Mvc\Router\Route
 */
class BaseRoute implements RouteInterface
{

    /**
     * {@inheritdoc}
     */
    public function add(RouterInterface $router, Route $route)
    {
        $router
            ->add($route->getRoute(), $route->getPaths())
            ->setName($route->getName())
            ->setHostName($route->getParam('hostname'));
    }
}
