<?php
/**
 * @author Sławomir Żytko <slawek@amsterdam-standard.pl>
 * @copyright (c) 2014, Amsterdam Standard
 */

namespace Vegas\Bootstrap;

use Phalcon\Config;
use Phalcon\DI\FactoryDefault;
use Phalcon\DI;
use Phalcon\Mvc\Dispatcher;
use Vegas\Mvc\Router\Adapter\Standard;
use Vegas\Mvc\Router;

trait RoutesInitializerTrait
{
    /**
     * Initializes routing
     */
    protected function initRoutes(Config $config)
    {
        //setups router
        $routerAdapter = new Standard($this->getDI());
        $router = new Router($this->getDI(), $routerAdapter);

        //adds routes defined in modules
        $modules = $this->getApplication()->getModules();
        foreach ($modules as $module) {
            $router->addModuleRoutes($module);
        }

        //adds routes defined in default file
        $defaultRoutesFile = $config->application->configDir
            . DIRECTORY_SEPARATOR
            . 'routes.php';
        if (file_exists($defaultRoutesFile)) {
            $routes = require $defaultRoutesFile;
            if (is_array($routes)) {
                $router->addRoutes($routes);
            }
        }

        //setup router rules
        $router->setup();

        //registers router into DI
        $this->getDI()->set('router', $router->getRouter());
    }

    abstract public function getApplication();

    abstract public function getDI();
}
 