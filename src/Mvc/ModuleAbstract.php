<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Mvc;

use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;

/**
 * Class ModuleAbstract
 * @package Vegas\Mvc
 */
abstract class ModuleAbstract implements ModuleDefinitionInterface
{
    /**
     * Current module namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Current directory name
     *
     * @var string
     */
    protected $dir;

    /**
     * Registers module autoloaders
     */
    public function registerAutoloaders(\Phalcon\DiInterface $di = null)
    {
        $this->registerControllerScopesAutoloader();
    }

    /**
     * Registers controllers scopes
     */
    public function registerControllerScopesAutoloader()
    {
        $namespaces = array();
        $directoryIterator = new \DirectoryIterator($this->dir . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR);
        foreach ($directoryIterator as $directory) {
            if ($directory->isDot()) {
                continue;
            }

            if (strstr($directory->getFileName(),'.php')) {
                $namespaces[$this->namespace . '\Controllers'] =
                    $this->dir . DIRECTORY_SEPARATOR . 'controllers';
            } else {
                $namespaces[$this->namespace . '\Controllers\\' . ucfirst($directory->getFileName())] =
                    $this->dir . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $directory->getFileName();
            }
        }

        $loader = new Loader();
        $loader->registerNamespaces($namespaces, true);
        $loader->register();
    }

    /**
     * Registers dispatcher namespace, view and application plugins
     *
     * @param \Phalcon\DiInterface $di
     */
    public function registerServices(\Phalcon\DiInterface $di)
    {
        $this->registerDispatcherNamespace($di);
        $this->registerViewComponent($di);
        $this->registerPlugins($di);
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
            $dispatcherPlugin = $reflectionClass->newInstance();
            if ($dispatcherPlugin instanceof InjectionAwareInterface) {
                $reflectionClass->getMethod('setDI')->invoke($dispatcherPlugin, $di);
            }
            $eventsManager->attach($plugin['attach'], $dispatcherPlugin);
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
            $view = new View($di->get('config')->application->view, $viewDir);

            if (file_exists($viewDir)) {
                $view->setViewsDir($viewDir);
            }
            
            $view->setEventsManager($di->getShared('eventsManager'));
            return $view;
        });
    }

    /**
     * Add default namespace to dispatcher.
     *
     * @param $di
     */
    protected function registerDispatcherNamespace($di)
    {
        $dispatcher = $di->get('dispatcher');

        $di->set('dispatcher', function() use ($dispatcher) {
            $dispatcher->setDefaultNamespace($this->namespace.'\Controllers');
            return $dispatcher;
        });
    }
}
