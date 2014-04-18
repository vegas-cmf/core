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
 
namespace Vegas\Tests\Mvc;

use Vegas\Http\Method;
use Vegas\Mvc\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{

    private $testRoutes = array(
        'statictest' => array(
            'route' => 'static/test',
            'paths' => array(
                'controller' => 'statictest',
                'action' => 'test'
            ),
            'type' => 'static',
            'params' => array()
        ),
        'test' => array(
            'route' => 'test/edit',
            'paths' => array(
                'controller' => 'test'
            ),
            'params' => array()
        ),
        'articles' => array(
            'route' => 'articles/list',
            'paths' => array(
                'controller' => 'articles'
            ),
            'type' => 'rest',
            'params' => array(
                'via' => array(Method::GET, Method::POST)
            )
        ),
        'products' => array(
            'route' => 'products',
            'paths' => array(
                'controller' => 'products'
            ),
            'type' => 'rest',
            'params' => array()
        )
    );

    public function testRouteDefinition()
    {
        $allMethods = array(
            Method::GET, Method::POST, Method::DELETE, Method::HEAD,
            Method::OPTIONS, Method::PATCH, Method::PUT
        );
        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(false);
        $router = new \Vegas\Mvc\Router($routerAdapter);
        $router->addRoutes($this->testRoutes);

        $router->setup();

        $this->assertEquals(count($router->getRouter()->getRoutes()), count($this->testRoutes));

        //checks HTTP methods for REST route type
        foreach ($router->getRouter()->getRoutes() as $i => $route) {
            $this->assertNotEmpty($this->testRoutes[$route->getName()]);
            $routeDefinition = $this->testRoutes[$route->getName()];
            if (isset($routeDefinition['type']) && $routeDefinition['type'] == Router::REST_ROUTE) {
                if (isset($routeDefinition['params']) && isset($routeDefinition['params']['via'])) {
                    $this->assertSameSize($routeDefinition['params']['via'],  $route->getHttpMethods());
                } else {
                    $this->assertSameSize($allMethods, $route->getHttpMethods());
                }
            }
        }

        //checks if static route is added in the end
        $routes = $router->getRouter()->getRoutes();
        $lastRoute = $routes[count($routes)-1];
        $this->assertEquals($lastRoute->getName(), 'statictest');

        //handle non-existing route type
        $failRoute = array('fake' => array(
            'route' => 'fakeurl',
            'paths' => array(
                'controller' => 'fake'
            ),
            'type' => 'wrongtype',
            'params' => array()
        ));

        $this->setExpectedException('\Vegas\Mvc\Router\Exception\InvalidRouteTypeException');
        $router->addRoute($failRoute);
        $router->setup();


    }
} 