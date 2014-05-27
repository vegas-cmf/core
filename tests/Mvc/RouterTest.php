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

use Phalcon\DI;
use Vegas\Http\Method;
use Vegas\Mvc\Module\ModuleLoader;
use Vegas\Mvc\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{

    private $testRoutes = array(
        'statictest' => array(
            'route' => '/static/qwerty',
            'paths' => array(
                'controller' => 'statictest',
                'action' => 'test'
            ),
            'type' => 'static',
            'params' => array()
        ),
        'nonstatictest' => array(
            'route' => '/static/{page}',
            'paths' => array(
                'controller' => 'nonstatictest',
                'action' => 'test'
            ),
            'type' => 'default',
            'params' => array()
        ),
        'test' => array(
            'route' => '/test/edit',
            'paths' => array(
                'controller' => 'test'
            ),
            'params' => array()
        ),
        'articles' => array(
            'route' => '/articles',
            'paths' => array(
                'controller' => 'articles'
            ),
            'type' => 'rest',
            'params' => array(
                'actions' => array(
                    '/' => array(
                        'index' => Method::GET,
                        'create' => Method::POST
                    )
                )
            )
        ),
        'products' => array(
            'route' => '/products',
            'paths' => array(
                'controller' => 'products'
            ),
            'type' => 'rest',
            'params' => array(
                'actions' => array(
                    '/' =>  array(
                        'index' =>  Method::GET,
                        'create'  =>  Method::POST
                    ),
                    '/{id}' =>  array(
                        'show' => Method::GET,
                        'update' => Method::PUT,
                        'delete' => Method::DELETE
                    ),
                )
            )
        ),
        'galleries' => array(
            'route' =>  '/galleries',
            'paths' => array(
                'controller' => 'galleries'
            ),
            'type' => 'rest'
        ),
        'dashboard' =>  array(
            'route' =>  '/',
            'paths' =>  array(
                'controller'    =>  'dashboard',
                'action'    =>  'index'
            ),
            'params' => array(
                'hostname'  =>  'test.vegas.com'
            )
        )
    );

    public function testRouteDefinition()
    {
        $_SERVER['HTTP_HOST'] = 'vegas.dev';

        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes($this->testRoutes);

        $route = new Router\Route('test', end($this->testRoutes));
        $this->assertInternalType('array', $route->getParams());
        $this->assertInternalType('string', $route->getRoute());
        $this->assertInternalType('array', $route->getPaths());
        $this->assertInternalType('string', $route->getName());


        $router->setup();

        $this->assertNotEmpty($router->getRouter()->getRoutes());

        $this->assertNotEmpty($router->getRouter()->getRouteByName('articles/index'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('articles/create'));
        $this->assertEmpty($router->getRouter()->getRouteByName('articles/update'));
        $this->assertEmpty($router->getRouter()->getRouteByName('articles/delete'));
        $this->assertEmpty($router->getRouter()->getRouteByName('articles/show'));

        $this->assertEquals(Method::GET, $router->getRouter()->getRouteByName('articles/index')->getHttpMethods());
        $this->assertEquals(Method::POST, $router->getRouter()->getRouteByName('articles/create')->getHttpMethods());

        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/create'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/delete'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/show'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/index'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/update'));

        $this->assertEquals(Method::POST, $router->getRouter()->getRouteByName('products/create')->getHttpMethods());
        $this->assertEquals(Method::DELETE, $router->getRouter()->getRouteByName('products/delete')->getHttpMethods());
        $this->assertEquals(Method::GET, $router->getRouter()->getRouteByName('products/show')->getHttpMethods());
        $this->assertEquals(Method::GET, $router->getRouter()->getRouteByName('products/index')->getHttpMethods());
        $this->assertEquals(Method::PUT, $router->getRouter()->getRouteByName('products/update')->getHttpMethods());

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

    public function testRouteMatching()
    {
        $_SERVER['HTTP_HOST'] = 'vegas.dev';

        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes($this->testRoutes);

        $router->setup();

        $testCorrectRoutes = array(
            '/static/test',
            '/test/edit'
        );

        $defaultRouter = $router->getRouter();
        foreach ($testCorrectRoutes as $url) {
            $defaultRouter->handle($url);
            $this->assertTrue($defaultRouter->wasMatched());
        }

        /**
         * Helper function for handling specified URI
         *
         * @param $method
         * @param $uri
         */
        $handleUri = function($method, $uri) use ($defaultRouter) {
            $this->setRequestMethod($method);
            $defaultRouter->handle($uri);
            $this->assertTrue($defaultRouter->wasMatched());
        };

        $handleUri(Method::GET, '/products/123');
        $handleUri(Method::PUT, '/products/123');
        $handleUri(Method::DELETE, '/products/123');

        $handleUri(Method::POST, '/products/');
        $handleUri(Method::GET, '/products/');

        $handleUri(Method::GET, '/articles');
        $handleUri(Method::POST, '/articles');

        //non existing routes
        $this->setExpectedException('\PHPUnit_Framework_ExpectationFailedException');
        $handleUri(Method::POST, '/products/123');
        $handleUri(Method::DELETE, '/products');
        $handleUri(Method::PUT, '/products');
        $handleUri(Method::PUT, '/products/123');
        $handleUri(Method::DELETE, '/articles/1234');
        $handleUri(Method::PUT, '/articles/1234');
        $handleUri(Method::PUT, '/articles');
        $handleUri(Method::GET, '/articles/1234');
    }

    public function testStaticRoutes()
    {
        $_SERVER['HTTP_HOST'] = 'vegas.dev';

        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes($this->testRoutes);

        $router->setup();

        $defaultRouter = $router->getRouter();

        $defaultRouter->handle('/static/qwerty');
        $this->assertNotEmpty($defaultRouter->getMatchedRoute());
        $matchedRoute = $defaultRouter->getMatchedRoute();
        $paths = $matchedRoute->getPaths();
        $this->assertEquals('statictest', $paths['controller']);

        $defaultRouter->handle('/static/asdfgh');
        $this->assertNotEmpty($defaultRouter->getMatchedRoute());
        $matchedRoute = $defaultRouter->getMatchedRoute();
        $paths = $matchedRoute->getPaths();
        $this->assertEquals('nonstatictest', $paths['controller']);
    }

    public function testHostNameConstraints()
    {
        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes($this->testRoutes);

        $router->setup();

        $defaultRouter = $router->getRouter();

        $_SERVER['HTTP_HOST'] = 'test.vegas.com';
        $defaultRouter->handle('/');
        $this->assertNotEmpty($defaultRouter->getMatchedRoute());
        $matchedRoute = $defaultRouter->getMatchedRoute();
        $paths = $matchedRoute->getPaths();
        $this->assertEquals('dashboard', $paths['controller']);
    }

    public function testModuleRoutes()
    {
        $_SERVER['HTTP_HOST'] = 'vegas.dev';

        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);

        $modules = ModuleLoader::dump(DI::getDefault());
        foreach ($modules as $module) {
            $router->addModuleRoutes($module);
        }

        $router->setup();

        $defaultRouter = $router->getRouter();

        $defaultRouter->handle('/test/fake/test');

        $this->assertNotEmpty($defaultRouter->getMatchedRoute());
        $this->assertEquals('Backend\Fake', $defaultRouter->getControllerName());
        $this->assertEquals('test', $defaultRouter->getActionName());
    }

    private function setRequestMethod($method)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
    }
}