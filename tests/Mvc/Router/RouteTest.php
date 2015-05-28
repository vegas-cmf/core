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

namespace Vegas\Tests\Mvc\Router;

use Vegas\Mvc\Router\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldMapConstructorParametersToClassProperties()
    {
        $routeArray = [
            'route' => '/url',
            'paths' => array(
                'module' => 'module',
                'controller' => 'controller',
                'action' => 'action'
            ),
            'type' => 'static',
            'params' => array(
                'param_1' => 'value_1',
                'param_2' => 'value_2',
                'param_3' => 'value_3',
                'auth' => array('auth', 'authAdmin')
            )
        ];

        $route = new Route('test', $routeArray);

        $this->assertEquals($route->getName(), 'test');
        $this->assertArrayHasKey('module', $route->getPaths());
        $this->assertArrayHasKey('controller', $route->getPaths());
        $this->assertArrayHasKey('action', $route->getPaths());
        $this->assertEquals('value_1', $route->getParam('param_1'));
        $this->assertEquals('value_2', $route->getParam('param_2'));
        $this->assertEquals('value_3', $route->getParam('param_3'));
        $this->assertSame(['auth', 'authAdmin'], $route->getParam('auth'));
        $this->assertEquals('/url', $route->getRoute());

        $this->assertInternalType('array', $route->getParams());
        $this->assertInternalType('string', $route->getRoute());
        $this->assertInternalType('array', $route->getPaths());
        $this->assertInternalType('string', $route->getName());
    }

    public function testShouldThrowExceptionForInvalidName()
    {
        $exception = null;
        try {
            new Route(false, []);
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Mvc\Router\Exception\InvalidRouteNameException', $exception);
    }

    public function testShouldThrowExceptionForInvalidPaths()
    {
        $exception = null;
        try {
            new Route('test', []);
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Mvc\Router\Exception\InvalidRoutePathsException', $exception);
    }
} 