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
 
namespace Vegas\Mvc\Router;
use Vegas\Mvc\Router\Exception\InvalidRouteNameException;
use Vegas\Mvc\Router\Exception\InvalidRoutePathsException;

/**
 * Class Route
 * Object representation of route
 *
 * @package Vegas\Mvc\Router
 */
class Route
{
    /**
     * Simplify route name
     *
     * @var string
     * @internal
     */
    private $name;

    /**
     * Route URL
     *
     * @var string
     * @internal
     */
    private $route;

    /**
     * Contains module/controller/action which will be executed when route is matched
     *
     * @var array
     * @internal
     */
    private $paths;

    /**
     * Optional route definition parameters
     * Available keys:
     *
     *      actions     -   allows to specify resource actions with HTTP methods, useful for building REST API
     *
     * @var array
     * @internal
     */
    private $params = [];

    /**
     * Constructor
     *
     * @param $name     Route name
     * @param $routeArray
     * @throws Exception\InvalidRouteNameException
     * @throws Exception\InvalidRoutePathsException
     */
    public function __construct($name, $routeArray)
    {
        if (!$name) {
            throw new InvalidRouteNameException();
        }
        $this->name = $name;

        //route is optional
        $this->route = isset($routeArray['route']) ? $routeArray['route'] : '';

        if (!isset($routeArray['paths']) || empty($routeArray['paths'])) {
            throw new InvalidRoutePathsException();
        }
        $this->paths = $routeArray['paths'];

        //params are optional
        $this->params = isset($routeArray['params']) ? $routeArray['params'] : array();
    }

    /**
     * Returns name of route
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Returns route paths
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Returns additional route params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Return indicated param if exists
     *
     * @param $param
     * @return null
     */
    public function getParam($param)
    {
        if (array_key_exists($param, $this->params)) {
            return $this->params[$param];
        }

        return null;
    }

    /**
     * Adds route param
     *
     * @param $param
     * @param $value
     * @return $this
     */
    public function setParam($param, $value)
    {
        $this->params[$param] = $value;

        return $this;
    }

    /**
     * Determines if route has an indicated param
     *
     * @param $param
     * @return null
     */
    public function hasParam($param)
    {
        return array_key_exists($param, $this->params);
    }
}
