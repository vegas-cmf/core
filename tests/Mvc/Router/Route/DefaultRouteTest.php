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

class DefaultRouteTest extends \PHPUnit_Framework_TestCase
{
//
//    public function testShouldAddDefaultRouteToRouter()
//    {
//        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
//
//        $route = new Route('def', [
//            'paths' => [
//                'module' => 'Def',
//                'controller' => 'Def',
//                'action' => 'def'
//            ],
//            'params' => [
//                'p1' => 'v1'
//            ]
//        ]);
//
//        $defaultRoute = new Route\DefaultRoute();
//        $defaultRoute->add($router, $route);
//
//        $this->assertNotEmpty($router->getDefaults());
//        $this->assertArrayHasKey('module', $router->getDefaults());
//        $this->assertArrayHasKey('controller', $router->getDefaults());
//        $this->assertArrayHasKey('action', $router->getDefaults());
//        $this->assertArrayHasKey('params', $router->getDefaults());
//        $this->assertEquals('Def', $router->getDefaults()['module']);
//        $this->assertEquals('Def', $router->getDefaults()['controller']);
//        $this->assertEquals('def', $router->getDefaults()['action']);
//        $this->assertEquals('v1', $router->getDefaults()['params']['p1']);
//    }
//
//    public function testShouldUseDefaultActionForRouteWithoutActionInPaths()
//    {
//        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
//
//        $route = new Route('def', [
//            'paths' => [
//                'module' => 'Def',
//                'controller' => 'DefController',
//                'action' => 'def'
//            ]
//        ]);
//
//        $defaultRoute = new Route\DefaultRoute();
//        $defaultRoute->add($router, $route);
//
//        $testRoute = new Route('test', [
//            'route' => '/test',
//            'paths' => [
//                'module' => 'Test',
//                'controller' => 'TestCon'
//            ],
//            'params' => [
//                'param' => 'value'
//            ]
//        ]);
//        $baseRoute = new Route\BaseRoute();
//        $baseRoute->add($router, $testRoute);
//
//        $router->handle('/test');
//
//        $matchedRoute = $router->getMatchedRoute();
//
//        $this->assertEquals($route->getPaths()['action'], $router->getActionName());
//        $this->assertEquals($testRoute->getPaths()['controller'], $router->getControllerName());
//        $this->assertEquals($testRoute->getPaths()['module'], $router->getModuleName());
//        $this->assertEquals($testRoute->getRoute(), $matchedRoute->getPattern());
//        $this->assertEquals($testRoute->getPaths()['controller'], $matchedRoute->getPaths()['controller']);
//        $this->assertEquals($testRoute->getPaths()['module'], $matchedRoute->getPaths()['module']);
//        $this->assertArrayNotHasKey('action', $testRoute->getPaths());
//    }
} 