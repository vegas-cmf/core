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
use Vegas\Mvc\Router\Route;

class StaticRouteTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldAddStaticRouteToRouter()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());

        $route = new Route('static', [
            'route' => '/static',
            'paths' => [
                'module' => 'Test',
                'controller' => 'Static',
                'action' => 'test'
            ],
            'type' => 'static'
        ]);

        $staticRoute = new Route\StaticRoute();
        $staticRoute->add($router, $route);

        $this->assertNotEmpty($router->getRoutes());
        $this->assertNotNull($router->getRouteByName('static'));
        $testRoute = $router->getRouteByName('static');
        $this->assertInstanceOf('\Phalcon\Mvc\Router\RouteInterface', $testRoute);
        $this->assertEquals('Test', $testRoute->getPaths()['module']);
        $this->assertEquals('Static', $testRoute->getPaths()['controller']);
        $this->assertEquals('test', $testRoute->getPaths()['action']);
        $this->assertEquals('/static', $testRoute->getPattern());
    }

    public function testShouldMatchStaticRoute()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());

        $route = new Route('static', [
            'route' => '/static',
            'paths' => [
                'module' => 'Test',
                'controller' => 'Static',
                'action' => 'test'
            ],
            'type' => 'static'
        ]);

        $staticRoute = new Route\StaticRoute();
        $staticRoute->add($router, $route);

        $router->handle('/static');

        $matchedRoute = $router->getMatchedRoute();
        $this->assertNotNull($matchedRoute);

        $this->assertEquals($matchedRoute->getName(), $route->getName());
        $this->assertEquals($matchedRoute->getPaths()['module'], $route->getPaths()['module']);
        $this->assertEquals($matchedRoute->getPaths()['controller'], $route->getPaths()['controller']);
        $this->assertEquals($matchedRoute->getPaths()['action'], $route->getPaths()['action']);
        $this->assertEquals($matchedRoute->getPattern(), $route->getRoute());
    }
} 