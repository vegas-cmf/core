<?php
namespace Vegas\Mvc;

use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Vegas\Mvc\Dispatcher\Events\BeforeException;
use Vegas\Mvc\Module\SubModuleManager;

/**
 * Class ModuleAbstract
 * @package Vegas\Mvc
 */
abstract class ModuleAbstract implements ModuleDefinitionInterface
{
    protected $namespace;
    protected $dir;
    
    public function registerAutoloaders()
    {
        $this->registerSubModulesAutoloader();
        $this->registerFormsAutoloader();
    }

    public function registerSubModulesAutoloader()
    {
        $namespaces = array();
        foreach (SubModuleManager::getSubModules() as $subModule) {
            $namespaces[$this->namespace . '\Controllers\\' . ucfirst($subModule)] =
                $this->dir . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $subModule;
        }

        $loader = new Loader();
        $loader->registerNamespaces($namespaces, true);
        $loader->register();
    }

    public function registerFormsAutoloader()
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            $this->namespace . '\Forms' =>  $this->dir . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR
        ), true);
        $loader->register();
    }
    
    public function registerServices($di)
    {
        $this->registerDispatcher($di);
        $this->registerScaffolding($di);
        $this->registerViewComponent($di);
        $this->registerPlugins($di);
    }
    
    protected function registerDispatcher($di)
    {
        $di->set('dispatcher', function() use ($di) {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace($this->namespace."\Controllers");

            $eventsManager = $di->getShared('eventsManager');
            $eventsManager->attach('dispatch:beforeException', BeforeException::fire());

            $dispatcher->setEventsManager($eventsManager);
            
            return $dispatcher;
        });
    }

    protected function registerPlugins($di)
    {
        $dispatcher = $di->get('dispatcher');
        $eventsManager = $di->getShared('eventsManager');
        $plugins = $di->get('config')->plugins;
        foreach ((array) $plugins as $pluginName => $plugin) {
            $className = $plugin['class'];
            $reflectionClass = new \ReflectionClass($className);
            $authenticationPlugin = $reflectionClass->newInstance();
            $reflectionClass->getMethod('setDI')->invoke($authenticationPlugin, $di);
            $eventsManager->attach($plugin['attach'], $authenticationPlugin);
        }
        $dispatcher->setEventsManager($eventsManager);
    }
    
    protected function registerScaffolding($di)
    {
        $adapter = new \Vegas\DI\Scaffolding\Adapter\Mongo;
        $di->set('scaffolding', new \Vegas\DI\Scaffolding($adapter));
    }
    
    protected function registerViewComponent($di)
    {
        $di->set('view', function() use ($di) {
            $view = new View($di->get('config')->application->view->toArray());
            if (file_exists($this->dir . '/views')) {
                $view->setViewsDir($this->dir.'/views/');
            }
            
            return $view;
        });
    }
}
