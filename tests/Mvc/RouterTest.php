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
 
namespace Vegas\Tests\Mvc;

use Phalcon\DI;
use Vegas\Http\Method;
use Vegas\Mvc\Module\Loader as ModuleLoader;
use Vegas\Mvc\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $_SERVER['HTTP_HOST'] = 'vegas.dev';
    }

    private $testRoutes = array(
        'default' => [
            'paths' => [
                'action' => 'index'
            ],
            'type' => 'default'
        ],
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
            'type' => 'base',
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
                        'index' => Method::GET,
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
                'hostname'  =>  'test.vegas.dev'
            )
        ),
        'notfound' => array(
            'route' =>  '/not-found',
            'paths' => array(
                'controller' => 'error',
                'action' => 'error404'
            ),
            'type' => 'notfound'
        )
    );

    public function testShouldContainGivenRouterAdapter()
    {
        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);

        $this->assertInstanceOf('\Vegas\Mvc\Router\Adapter\Standard', $router->getRouter());
        $this->assertSame($routerAdapter, $router->getRouter());
    }

    public function testShouldAddRoutes()
    {
        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes($this->testRoutes);

        $router->setup();

        $this->assertNotEmpty($router->getRouter()->getRoutes());

        $this->assertNotEmpty($router->getRouter()->getRouteByName('statictest'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('nonstatictest'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('test'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('articles/index'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('articles/create'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/index'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/create'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/{id}/index'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/{id}/show'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/{id}/update'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('products/{id}/delete'));
        $this->assertEmpty($router->getRouter()->getRouteByName('galleries'));
        $this->assertEmpty($router->getRouter()->getRouteByName('notfound'));
        $this->assertEmpty($router->getRouter()->getRouteByName('default'));
        $this->assertNotEmpty($router->getRouter()->getRouteByName('dashboard'));
    }

    public function testShouldThrowExceptionForInvalidRouteType()
    {
        $failRoute = array('fake' => array(
            'route' => 'fakeurl',
            'paths' => array(
                'controller' => 'fake'
            ),
            'type' => 'wrongtype',
            'params' => array()
        ));

        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);

        $exception = null;
        try {
            $router->addRoute($failRoute);
            $router->setup();
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Mvc\Router\Exception\InvalidRouteTypeException', $exception);
    }

    public function testShouldMatchStaticRoute()
    {
        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes($this->testRoutes);

        $router->setup();

        $defaultRouter = $router->getRouter();

        $staticRoute = $defaultRouter->getRouteByName('statictest');

        $defaultRouter->handle('/static/qwerty');
        $matchedRoute = $defaultRouter->getMatchedRoute();
        $this->assertNotEmpty($matchedRoute);
        $this->assertEquals($staticRoute->getName(), $matchedRoute->getName());
        $this->assertEquals('statictest', $matchedRoute->getPaths()['controller']);
        $this->assertEquals('test', $matchedRoute->getPaths()['action']);
        $this->assertEquals($staticRoute->getPattern(), $matchedRoute->getPattern());

        $nonStaticRoute = $defaultRouter->getRouteByName('nonstatictest');

        $defaultRouter->handle('/static/asdfgh');
        $matchedRoute = $defaultRouter->getMatchedRoute();
        $this->assertNotEmpty($matchedRoute);
        $this->assertEquals($nonStaticRoute->getName(), $matchedRoute->getName());
        $this->assertEquals('nonstatictest', $matchedRoute->getPaths()['controller']);
        $this->assertEquals('test', $matchedRoute->getPaths()['action']);
        $this->assertEquals($nonStaticRoute->getPattern(), $matchedRoute->getPattern());
    }

    public function testShouldAddModuleRoutes()
    {
        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);

        $moduleLoader = new ModuleLoader(DI::getDefault());
        $modules = $moduleLoader->dump(
            TESTS_ROOT_DIR . '/fixtures/app/modules/',
            TESTS_ROOT_DIR . '/fixtures/app/config/'
        );
        foreach ($modules as $module) {
            $router->addModuleRoutes($module);
        }
        $router->setup();

        $defaultRouter = $router->getRouter();
        $defaultRouter->handle('/test/fake/test');

        $route = $defaultRouter->getRouteByName('testfake');

        $matchedRoute = $defaultRouter->getMatchedRoute();
        $this->assertNotEmpty($matchedRoute);
        $this->assertEquals($route->getPaths()['controller'], $defaultRouter->getControllerName());
        $this->assertEquals($route->getPaths()['action'], $defaultRouter->getActionName());
        $this->assertEquals($route->getPaths()['module'], $defaultRouter->getModuleName());
        $this->assertEquals($route->getName(), $matchedRoute->getName());
        $this->assertEquals($route->getPaths()['action'], $matchedRoute->getPaths()['action']);
        $this->assertEquals($route->getPaths()['controller'], $matchedRoute->getPaths()['controller']);
        $this->assertEquals($route->getPaths()['module'], $matchedRoute->getPaths()['module']);
    }

    public function testShouldMatchRouteWithHostNameResolvedFromHttpHost()
    {
        $_SERVER['HTTP_HOST'] = 'test.vegas.dev';

        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes([
            'test' => [
                'route' => '/',
                'paths' => [
                    'module' => 'Mod',
                    'controller' => 'Con',
                    'action' => 'Act'
                ]
            ]
        ]);

        $router->setup();

        $defaultRouter = $router->getRouter();
        $route = $defaultRouter->getRouteByName('test');

        $defaultRouter->handle('/');
        $matchedRoute = $defaultRouter->getMatchedRoute();
        $this->assertNotEmpty($matchedRoute);
        $this->assertEquals($route->getPaths()['module'], $matchedRoute->getPaths()['module']);
        $this->assertEquals($route->getPaths()['controller'], $matchedRoute->getPaths()['controller']);
        $this->assertEquals($route->getPaths()['action'], $matchedRoute->getPaths()['action']);
    }

    public function testShouldMatchRouteWithHostNameResolvedFromApplicationConfig()
    {
        $_SERVER['HTTP_HOST'] = 'test.vegas.dev';
        DI::getDefault()->get('config')->application->hostname = 'test.vegas.dev';

        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes([
            'test' => [
                'route' => '/',
                'paths' => [
                    'module' => 'Mod',
                    'controller' => 'Con',
                    'action' => 'Act'
                ]
            ]
        ]);

        $router->setup();

        $defaultRouter = $router->getRouter();
        $route = $defaultRouter->getRouteByName('test');

        $defaultRouter->handle('/');
        $matchedRoute = $defaultRouter->getMatchedRoute();
        $this->assertNotEmpty($matchedRoute);
        $this->assertEquals($route->getPaths()['module'], $matchedRoute->getPaths()['module']);
        $this->assertEquals($route->getPaths()['controller'], $matchedRoute->getPaths()['controller']);
        $this->assertEquals($route->getPaths()['action'], $matchedRoute->getPaths()['action']);
    }

    public function testShouldNotMatchRouteWithHostNameResolvedFromHttpHost()
    {
        $_SERVER['HTTP_HOST'] = 'test.vegas.dev';
        DI::getDefault()->get('config')->application->hostname = null;

        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes([
            'test' => [
                'route' => '/',
                'paths' => [
                    'module' => 'Mod',
                    'controller' => 'Con',
                    'action' => 'Act'
                ],
                'params' => [
                    'hostname' => 'test2.vegas.dev'
                ]
            ]
        ]);

        $router->setup();

        $defaultRouter = $router->getRouter();

        $defaultRouter->handle('/');
        $matchedRoute = $defaultRouter->getMatchedRoute();
        $this->assertEmpty($matchedRoute);;
    }

    public function testShouldNotMatchRouteWithHostNameResolvedFromApplicationConfig()
    {
        $_SERVER['HTTP_HOST'] = 'test.vegas.dev';
        DI::getDefault()->get('config')->application->hostname = 'test2.vegas.dev';

        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes([
            'test' => [
                'route' => '/',
                'paths' => [
                    'module' => 'Mod',
                    'controller' => 'Con',
                    'action' => 'Act'
                ]
            ]
        ]);

        $router->setup();

        $defaultRouter = $router->getRouter();

        $defaultRouter->handle('/');
        $matchedRoute = $defaultRouter->getMatchedRoute();
        $this->assertEmpty($matchedRoute);;
    }
    public function testShouldMatchRouteWithEmptyHostName()
    {
        $_SERVER['HTTP_HOST'] = null;
        DI::getDefault()->get('config')->application->hostname = null;

        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard(DI::getDefault());
        $router = new \Vegas\Mvc\Router(DI::getDefault(), $routerAdapter);
        $router->addRoutes([
            'test' => [
                'route' => '/',
                'paths' => [
                    'module' => 'Mod',
                    'controller' => 'Con',
                    'action' => 'Act'
                ]
            ]
        ]);

        $router->setup();

        $defaultRouter = $router->getRouter();
        $route = $defaultRouter->getRouteByName('test');

        $defaultRouter->handle('/');
        $matchedRoute = $defaultRouter->getMatchedRoute();
        $this->assertNotEmpty($matchedRoute);
        $this->assertEquals($route->getPaths()['module'], $matchedRoute->getPaths()['module']);
        $this->assertEquals($route->getPaths()['controller'], $matchedRoute->getPaths()['controller']);
        $this->assertEquals($route->getPaths()['action'], $matchedRoute->getPaths()['action']);
    }
}