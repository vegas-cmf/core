<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
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
                'action' => 'action',

                'auth' => array('auth', 'authAdmin')
            ),
            'type' => 'static',
            'params' => array()
        ];

        $route = new Route('test', $routeArray);

        $this->assertEquals($route->getName(), 'test');
        $this->assertArrayHasKey('module', $route->getPaths());
        $this->assertArrayHasKey('controller', $route->getPaths());
        $this->assertArrayHasKey('action', $route->getPaths());
        $this->assertArrayHasKey('auth', $route->getPaths());

    }
} 