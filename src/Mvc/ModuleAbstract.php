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
    }

    /**
     * Registers controllers in registered sub-modules
     */
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

    /**
     * Registers dispatcher, view, application plugins
     *
     * @param \Phalcon\DiInterface $di
     */
    public function registerServices($di)
    {
        $this->registerDispatcher($di);
        $this->registerViewComponent($di);
        $this->registerPlugins($di);
    }

    /**
     * Registers default dispatcher
     *
     * @param $di
     */
    protected function registerDispatcher($di)
    {
        $di->set('dispatcher', function() use ($di) {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace($this->namespace."\Controllers");

            $eventsManager = $di->getShared('eventsManager');
            $eventsManager->attach('dispatch:beforeException', BeforeException::getEvent());

            $dispatcher->setEventsManager($eventsManager);
            
            return $dispatcher;
        });
    }

    /**
     * Registers application plugins
     *
     * @param $di
     */
    protected function registerPlugins($di)
    {
        $dispatcher = $di->get('dispatcher');
        $eventsManager = $di->getShared('eventsManager');
        $plugins = $di->get('config')->plugins;
        foreach ((array) $plugins As $plugin) {
            $className = $plugin['class'];
            $reflectionClass = new \ReflectionClass($className);
            $authenticationPlugin = $reflectionClass->newInstance();
            $reflectionClass->getMethod('setDI')->invoke($authenticationPlugin, $di);
            $eventsManager->attach($plugin['attach'], $authenticationPlugin);
        }
        $dispatcher->setEventsManager($eventsManager);
    }

    /**
     * Registers views
     *
     * @param $di
     */
    protected function registerViewComponent($di)
    {
        $di->set('view', function() use ($di) {
            $viewDir = $this->dir . '/views';
            $view = new View($di->get('config')->application->view->toArray(), $viewDir);

            if (file_exists($viewDir)) {
                $view->setViewsDir($viewDir);
            }
            
            $view->setEventsManager($di->getShared('eventsManager'));
            return $view;
        });
    }
}
