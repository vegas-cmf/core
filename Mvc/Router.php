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

class Router extends \Phalcon\Mvc\Router
{
    private static $staticRoutes = array();

    public function addRoutesFromModule(array $module)
    {
        $routeArray = $this->getRouteArrayFromModulePath($module['path']);
        
        $this->addRoutesFromArray($routeArray);
    }
    
    private function getRouteArrayFromModulePath($path)
    {
        $path = dirname($path).'/config/routes.php';
        
        if (is_file($path)) {
            return require $path;
        } 
        
        return array();
    }
    
    public function addRoutesFromArray(array $routeArray)
    {
        foreach ($routeArray As $name => $route) {
            if (isset($route['static'])) {
                self::$staticRoutes[$name] = $route;
            } else {
                $this->add($route[0], $route[1])->setName($name);
            }
        }
    }

    public function addStaticRoutes()
    {
        foreach (self::$staticRoutes as $name => $route) {
            $this->add($route[0], $route[1])->setName($name);
        }
    }
}
