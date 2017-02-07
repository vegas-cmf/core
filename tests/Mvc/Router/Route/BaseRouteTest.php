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

use Phalcon\Di;
use Vegas\Mvc\Router\Route\BaseRoute;
use Vegas\Mvc\Router\Route;

class BaseRouteTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldAddBaseRouteToRouter()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(Di::getDefault());

        $route = new Route('test', [
            'route' => '/test',
            'paths' => [
                'module' => 'Test',
                'controller' => 'TestCon',
                'action' => 'test'
            ],
            'params' => [
                'param' => 'value',
                'hostname' => 'test.localhost'
            ]
        ]);

        $baseRoute = new BaseRoute();
        $baseRoute->add($router, $route);

        $this->assertNotEmpty($router->getRoutes());
        $this->assertNotNull($router->getRouteByName('test'));
        $testRoute = $router->getRouteByName('test');
        $this->assertInstanceOf('\Phalcon\Mvc\Router\RouteInterface', $testRoute);
        $this->assertEquals('test.localhost', $testRoute->getHostname());
        $this->assertEquals('Test', $testRoute->getPaths()['module']);
        $this->assertEquals('TestCon', $testRoute->getPaths()['controller']);
        $this->assertEquals('test', $testRoute->getPaths()['action']);
        $this->assertEquals('/test', $testRoute->getPattern());
    }

    public function testShouldMatchBaseRoute()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(Di::getDefault());

        $route = new Route('test', [
            'route' => '/test',
            'paths' => [
                'module' => 'Test',
                'controller' => 'TestCon',
                'action' => 'test'
            ],
            'params' => [
                'param' => 'value',
                'hostname' => 'test.localhost'
            ]
        ]);

        $baseRoute = new BaseRoute();
        $baseRoute->add($router, $route);

        //note. the HTTP_HOST is empty atm
        $router->handle('/test');
        $this->assertNull($router->getMatchedRoute());

        //hardcode the HTTP_HOST
        $_SERVER['HTTP_HOST'] = 'test.localhost';
        $router->handle('/test');
        $matchedRoute = $router->getMatchedRoute();

        $this->assertNotNull($matchedRoute);
        $this->assertEquals($route->getName(), $matchedRoute->getName());
        $this->assertEquals($matchedRoute->getPaths()['module'], $route->getPaths()['module']);
        $this->assertEquals($matchedRoute->getPaths()['controller'], $route->getPaths()['controller']);
        $this->assertEquals($matchedRoute->getPaths()['action'], $route->getPaths()['action']);
        $this->assertEquals($route->getRoute(), $matchedRoute->getPattern());
    }
} 