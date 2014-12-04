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
 
namespace Vegas\Tests\Mvc\Router\Route;

use Phalcon\DI;
use Vegas\Mvc\Router\Route\BaseRoute;
use Vegas\Mvc\Router\Route;

class NotFoundRouteTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldAddNotFoundRouteToRouter()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());

        $route = new Route('notfound', [
            'paths' => [
                'module' => 'Not',
                'controller' => 'Found',
                'action' => 'error404'
            ]
        ]);

        $notFoundRoute = new Route\NotfoundRoute();
        $notFoundRoute->add($router, $route);

        $reflectionObject = new \ReflectionObject($router);
        $notFoundProperty = $reflectionObject->getProperty('_notFoundPaths');
        $notFoundProperty->setAccessible(true);
        $routerNotFoundPaths = $notFoundProperty->getValue($router);

        $this->assertEquals($route->getPaths()['module'], $routerNotFoundPaths['module']);
        $this->assertEquals($route->getPaths()['controller'], $routerNotFoundPaths['controller']);
        $this->assertEquals($route->getPaths()['action'], $routerNotFoundPaths['action']);
    }

    public function testShouldUseNotFoundRoute()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());

        $route = new Route('notfound', [
            'paths' => [
                'module' => 'Not',
                'controller' => 'Found',
                'action' => 'error404'
            ]
        ]);

        $notFoundRoute = new Route\NotfoundRoute();
        $notFoundRoute->add($router, $route);

        $router->handle('/test');
        $matchedRoute = $router->getMatchedRoute();
        $this->assertNull($matchedRoute);
        $this->assertEquals($route->getPaths()['module'], $router->getModuleName());
        $this->assertEquals($route->getPaths()['controller'], $router->getControllerName());
        $this->assertEquals($route->getPaths()['action'], $router->getActionName());
    }
} 