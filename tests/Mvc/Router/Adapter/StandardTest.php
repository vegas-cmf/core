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
 
namespace Vegas\Tests\Mvc\Router\Adapter;

use Phalcon\Di;

class StandardTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldAdapterInstanceImplementRouterInterface()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(Di::getDefault());
        $this->assertInstanceOf('\Phalcon\Mvc\RouterInterface', $router);
    }

    public function testShouldNotKeepDefaultRoutes()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(Di::getDefault());
        $this->assertEmpty($router->getRoutes());
    }

    public function testShouldRemoveExtraSlashes()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(Di::getDefault());
        $router->add('/test', [
            'module' => 'module',
            'controller' => 'controller',
            'action' => 'action'
        ])->setName('test');

        $router->handle('/test/');
        $matchedRoute = $router->getMatchedRoute();
        $this->assertNotNull($matchedRoute);
        $this->assertEquals($matchedRoute->getName(), 'test');
        $this->assertEquals($matchedRoute->getPattern(), '/test');
    }

    public function testShouldNotMatchRouteWithExtraSlash()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(Di::getDefault());
        $router->removeExtraSlashes(false);
        $router->add('/test', [
            'module' => 'module',
            'controller' => 'controller',
            'action' => 'action'
        ])->setName('test');

        $router->handle('/test/');
        $this->assertNull($router->getMatchedRoute());
    }
} 