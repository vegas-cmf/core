<?php
namespace Vegas\Mvc;

use Vegas\Core;
use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Vegas\Mvc\Dispatcher\Events\BeforeException;

//use Phalcon\Events\Manager;

abstract class ModuleAbstract implements ModuleDefinitionInterface
{
    protected $namespace;
    protected $dir;
    
    public function registerAutoloaders()
    {
        $loader = new Loader();
        
        $loader->registerNamespaces(
            array(
                $this->namespace.'\Controllers\\'.Core::FRONTEND_NAMESPACE => $this->dir.'/controllers/frontend/',
                $this->namespace.'\Controllers\\'.Core::BACKEND_NAMESPACE  => $this->dir.'/controllers/backend/',
                $this->namespace.'\Forms'   => $this->dir.'/forms/'
            ), true
        );
        
        $loader->register();
    }
    
    public function registerServices($di)
    {
        $this->registerDispatcher($di);
        $this->registerScaffolding($di);
        $this->registerViewComponent($di);
        $this->registerMediaServices($di);
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
        $di->set('view', function() {
            $view = new View();
            if (file_exists($this->dir . '/views')) {
                $view->setViewsDir($this->dir.'/views/');
            }
            
            return $view;
        });
    }

    protected function registerMediaServices($di)
    {
        $di->set('uploader', function() {
            $uploader = new \Vegas\Media\Uploader();
            $uploader->setMaxSize('10MB')
                    ->setExtensions(array('jpg', 'png'))
                    ->setMimeTypes(array('image/jpeg', 'image/png'));
            
            return $uploader;
        });
        
        $di->set('fileWrapper', '\Vegas\Media\File\Wrapper', true);
    }
}
