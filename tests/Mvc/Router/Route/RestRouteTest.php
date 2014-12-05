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
use Phalcon\Mvc\Router\RouteInterface;
use Vegas\Http\Method;
use Vegas\Mvc\Router\Route\BaseRoute;
use Vegas\Mvc\Router\Route;

class RestRouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Helper function for handling specified URI
     *
     * @param $router
     * @param $method
     * @param $uri
     * @return \Phalcon\Mvc\Router\Route
     */
    private function handleUri($router, $method, $uri) {
        $_SERVER['REQUEST_METHOD'] = $method;
        $router->handle($uri);

        return $router->getMatchedRoute();
    }

    public function testShouldAddRestRouteToRouter()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());

        $route = new Route('rest', [
            'route' => '/rest',
            'paths' => [
                'module' => 'Test',
                'controller' => 'Rest'
            ],
            'type' => 'rest',
            'params' => [
                'actions' => [
                    '/' => [
                        'index' => Method::GET,
                        'create' => Method::POST
                    ]
                ]
            ]
        ]);

        $restRoute = new Route\RestRoute();
        $restRoute->add($router, $route);

        $this->assertNotEmpty($router->getRoutes());
        $this->assertFalse($router->getRouteByName('rest'));

        $this->assertEquals(Method::GET, $router->getRouteByName('rest/index')->getHttpMethods());
        $this->assertEquals(Method::POST, $router->getRouteByName('rest/create')->getHttpMethods());
    }

    public function testShouldMatchRestRoute()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());

        $route = new Route('rest', [
            'route' => '/rest',
            'paths' => [
                'module' => 'Test',
                'controller' => 'Rest'
            ],
            'type' => 'rest',
            'params' => [
                'actions' => [
                    '/' => [
                        'index' => Method::GET,
                        'create' => Method::POST
                    ]
                ]
            ]
        ]);

        $restRoute = new Route\RestRoute();
        $restRoute->add($router, $route);

        $indexRoute = $this->handleUri($router, 'GET', '/rest');
        $this->assertNotNull($indexRoute);
        $this->assertEquals('Test', $indexRoute->getPaths()['module']);
        $this->assertEquals('Rest', $indexRoute->getPaths()['controller']);
        $this->assertEquals('index', $indexRoute->getPaths()['action']);
        $this->assertEquals('/rest', $indexRoute->getPattern());

        $createRoute = $this->handleUri($router, 'POST', '/rest');
        $this->assertNotNull($createRoute);
        $this->assertEquals('Test', $createRoute->getPaths()['module']);
        $this->assertEquals('Rest', $createRoute->getPaths()['controller']);
        $this->assertEquals('create', $createRoute->getPaths()['action']);
        $this->assertEquals('/rest', $createRoute->getPattern());
    }

    public function testShouldNotMatchRestRouteWithWrongRequestMethod()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());

        $route = new Route('rest', [
            'route' => '/rest',
            'paths' => [
                'module' => 'Test',
                'controller' => 'Rest'
            ],
            'type' => 'rest',
            'params' => [
                'actions' => [
                    '/' => [
                        'index' => Method::GET,
                        'create' => Method::POST
                    ]
                ]
            ]
        ]);

        $restRoute = new Route\RestRoute();
        $restRoute->add($router, $route);

        $this->assertNull($this->handleUri($router, 'PUT', '/rest'));
        $this->assertNull($this->handleUri($router, 'DELETE', '/rest'));
        $this->assertNull($this->handleUri($router, 'PATCH', '/rest'));
        $this->assertNull($this->handleUri($router, 'OPTIONS', '/rest'));
        $this->assertNull($this->handleUri($router, 'HEAD', '/rest'));
    }
} 