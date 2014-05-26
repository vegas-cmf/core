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

/**
 * Class DefaultRoute
 * Default route type.
 *
 * @package Vegas\Mvc\Router\Route
 */
class DefaultRoute implements RouteInterface
{

    /**
     * {@inheritdoc}
     */
    public function add(\Phalcon\Mvc\RouterInterface $router, Route $route)
    {
        $newRoute = $router
            ->add($route->getRoute(), $route->getPaths())
            ->setName($route->getName());

        if ($route->getParam('hostname')) {
            $hostName = $route->getParam('hostname');
        } else {
            $hostName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
        }
        $newRoute->setHostName($hostName);
    }
} 