<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
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

    const DEFAULT_ROUTE = 'default';
    const STATIC_ROUTE = 'static';
    const REST_ROUTE = 'rest';

    /**
     * List of available route types
     * Note.
     *  Type are ordered ascending by priority.
     *  The static routes will be added in the end.
     *
     * @var array
     */
    private $routeTypes = array(
        self::DEFAULT_ROUTE => 0,
        self::REST_ROUTE => 1,
        self::STATIC_ROUTE => 2
    );

    /**
     * List of defined route rules
     *
     * @var array
     */
    private $routes = array();

    /**
     * @var \Phalcon\Mvc\RouterInterface
     */
    private $adapter;

    /**
     * Helper array for storing resolved route objects
     *
     * @var array
     */
    private $resolvedTypes = array();

    /**
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
     * @param $type
     * @return bool
     * @throws Router\Exception\InvalidRouteTypeException
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
                $routeType->add($this->getRouter(), new Route($name, $route));
            }
        }
    }

    /**
     * @return string|null
     */
    private function resolveDefaultHostName()
    {
        $request = $this->di->get('request');
        $config = $this->di->get('config');
        if (isset($config->application->host)) {
            $hostName = $config->application->hostname;
        } else if ($request->getServer('HTTP_HOST')) {
            $hostName = $request->getServer('HTTP_HOST');
        } else {
            $hostName = null;
        }

        return $hostName;
    }

    /**
     * @param $routeType
     * @return \Vegas\Mvc\Router\RouteInterface
     * @throws Router\Exception\InvalidRouteTypeException
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
