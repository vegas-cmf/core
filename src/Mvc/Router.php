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

namespace Vegas\Mvc;

use Phalcon\DI\InjectionAwareInterface;
use Phalcon\DiInterface;
use Vegas\DI\InjectionAwareTrait;
use Vegas\Mvc\Router\Exception\InvalidRouteTypeException;
use Vegas\Mvc\Router\Route;

/**
 * Class Router
 * @package Vegas\Mvc
 */
class Router implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    const BASE_ROUTE = 'base';
    const DEFAULT_ROUTE = 'default';
    const STATIC_ROUTE = 'static';
    const REST_ROUTE = 'rest';
    const NOTFOUND_ROUTE = 'notfound';

    /**
     * List of available route types
     * Note.
     *  Type are ordered ascending by priority.
     *  The static routes will be added in the end.
     *
     * @var array
     * @internal
     */
    private $routeTypes = array(
        self::BASE_ROUTE => 0,
        self::DEFAULT_ROUTE => 1,
        self::REST_ROUTE => 2,
        self::STATIC_ROUTE => 3,
        self::NOTFOUND_ROUTE => 4
    );

    /**
     * List of defined route rules
     *
     * @var array
     * @internal
     */
    private $routes = array();

    /**
     * @var \Phalcon\Mvc\RouterInterface
     * @internal
     */
    private $adapter;

    /**
     * Helper array for storing resolved route objects
     *
     * @var array
     * @internal
     */
    private $resolvedTypes = array();

    /**
     * Constructor
     * Sets router adapter
     *
     * @param DiInterface $di
     * @param \Phalcon\Mvc\RouterInterface $routerAdapter
     */
    public function __construct(DiInterface $di, \Phalcon\Mvc\RouterInterface $routerAdapter)
    {
        $this->setDI($di);
        $this->adapter = $routerAdapter;
    }

    /**
     * Adds multiple routes definition
     *
     * @param array $routesArray
     * @return $this
     */
    public function addRoutes(array $routesArray)
    {
        $this->routes = array_merge($this->routes, $routesArray);

        return $this;
    }

    /**
     * Adds single route definition
     *
     * @param array $routeArray
     * @return $this
     */
    public function addRoute(array $routeArray)
    {
        $routeName = reset(array_keys($routeArray));
        $route = reset(array_values($routeArray));
        $this->routes[$routeName] = $route;

        return $this;
    }

    /**
     * Adds module routes from specified path
     *
     * @param array $module
     * @return $this
     */
    public function addModuleRoutes(array $module)
    {
        $routeArray = $this->getRouteArrayFromModulePath($module['path']);
        $this->addRoutes($routeArray);

        return $this;
    }

    /**
     * Extracts routes from specified path
     *
     * @param $path
     * @return array|mixed
     * @internal
     */
    private function getRouteArrayFromModulePath($path)
    {
        $path = dirname($path).'/config/routes.php';

        if (file_exists($path) && is_file($path)) {
            return require $path;
        } 
        
        return array();
    }

    /**
     * Groups routes by types
     * @internal
     */
    private function groupRoutes()
    {
        $routes = array();
        foreach ($this->routeTypes as $type => $typeName) {
            $routes[$type] = array();
        }

        foreach ($this->routes as $routePattern => $route) {
            if (!isset($route['type'])) {
                $route['type'] = Router::DEFAULT_ROUTE;
            }

            $this->validateRouteType($route['type']);
            $routes[$route['type']][$routePattern] = $route;
        }

        $this->routes = $routes;
    }

    /**
     * Validates type of route
     *
     * @param $type
     * @return bool
     * @throws Router\Exception\InvalidRouteTypeException
     * @internal
     */
    private function validateRouteType($type)
    {
        if (!array_key_exists($type, $this->routeTypes)) {
            throw new InvalidRouteTypeException();
        }
        return true;
    }

    /**
     * Returns router adapter
     *
     * @return \Phalcon\Mvc\RouterInterface
     */
    public function getRouter()
    {
        return $this->adapter;
    }

    /**
     * Setups router
     * Adds rules to router
     */
    public function setup()
    {
        $this->groupRoutes();

        foreach ($this->routes as $type => $routes) {
            foreach ($routes as $name => $route) {
                $newRoute = new Route($name, $route);
                if (!$newRoute->hasParam('hostname')) {
                    $newRoute->setParam('hostname', $this->resolveDefaultHostName());
                }

                $routeType = $this->resolveRouteType($type);
                $routeType->add($this->getRouter(), $newRoute);
            }
        }
    }

    /**
     * Resolves hostname from $_SERVER['HTTP_HOST']
     *
     * @return string|null
     * @internal
     */
    private function resolveDefaultHostName()
    {
        $request = $this->di->get('request');
        $config = $this->di->get('config');
        if (isset($config->application->hostname)) {
            $hostName = $config->application->hostname;
        } else if ($request->getServer('HTTP_HOST')) {
            $hostName = $request->getServer('HTTP_HOST');
        } else {
            $hostName = null;
        }
        if (null !== $hostName) {
            $hostName = '(www\.)?' . $hostName;
        }

        return $hostName;
    }

    /**
     * Resolves route type
     *
     * @param $routeType
     * @return \Vegas\Mvc\Router\RouteInterface
     * @throws Router\Exception\InvalidRouteTypeException
     * @internal
     */
    private function resolveRouteType($routeType)
    {
        $typeClassName = sprintf('%sRoute', ucfirst($routeType));
        $classNamespace = __NAMESPACE__ . '\\Router\\Route\\' . $typeClassName;
        if (!array_key_exists($classNamespace, $this->resolvedTypes)) {
            $reflectionClass = new \ReflectionClass($classNamespace);
            $this->resolvedTypes[$classNamespace] = $reflectionClass->newInstance();
        }

        return $this->resolvedTypes[$classNamespace];
    }
}
