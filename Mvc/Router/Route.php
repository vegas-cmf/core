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

class Route
{
    /**
     * Name assigned to route definition
     *
     * @var string
     */
    private $name;

    /**
     *
     *
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $paths;

    /**
     * Optional route definition parameters
     * Available keys:
     *
     *      via     -   allows to specify HTTP methods, useful for building REST API
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

    public function getName()
    {
        return $this->name;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getParam($param)
    {
        if (array_key_exists($param, $this->params)) {
            return $this->params[$param];
        }

        return null;
    }
} 