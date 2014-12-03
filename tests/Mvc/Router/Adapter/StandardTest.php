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


use Phalcon\DI;

class StandardTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldAdapterInstanceImplementRouterInterface()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $this->assertInstanceOf('\Phalcon\Mvc\RouterInterface', $router);
    }

    public function testShouldRemoveExtraSlashes()
    {
        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
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
        $router = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
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