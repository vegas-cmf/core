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
 
namespace Vegas\Mvc\Router;

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
     */
    private $name;

    /**
     * Route URL
     *
     * @var string
     */
    private $route;

    /**
     * Contains module/controller/action which will be executed when route is matched
     *
     * @var array
     */
    private $paths;

    /**
     * Optional route definition parameters
     * Available keys:
     *
     *      actions     -   allows to specify resource actions with HTTP methods, useful for building REST API
     *
     * @var array
     */
    private $params = array();

    /**
     * @param $name     Name of route
     * @param $routeArray
     */
    public function __construct($name, $routeArray)
    {
        $this->name = $name;
        $this->route = $routeArray['route'];
        $this->paths = $routeArray['paths'];
        $this->params = isset($routeArray['params']) ? $routeArray['params'] : array();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
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
} 