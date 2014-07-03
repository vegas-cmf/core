<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Cli;

use Phalcon\DI\FactoryDefault\CLI;
use Vegas\BootstrapAbstract;
use Vegas\BootstrapInterface;
use Vegas\Cli\Exception as CliException;
use Vegas\Constants;
use Vegas\DI\ServiceProviderLoader;
use Vegas\Mvc\Module\ModuleLoader;
use Vegas\Mvc\Module\SubModuleManager;

class Bootstrap implements BootstrapInterface
{
    private $arguments;

    /**
     * @param \Phalcon\Config $config
     */
    public function __construct(\Phalcon\Config $config)
    {
        $this->config = $config;
        $this->di = new CLI();
        $this->console = new Console();
    }

    /**
     * Initializes loader
     * Registers library and plugin directory
     */
    protected function initLoader()
    {
        $loader = new \Phalcon\Loader();
        $loader->registerDirs(
            array(
                $this->config->application->libraryDir,
                $this->config->application->pluginDir,
                $this->config->application->tasksDir
            )
        )->register();
    }

    /**
     * Initializes application environment
     */
    protected function initEnvironment()
    {
        if (isset($this->config->application->environment)) {
            $env = $this->config->application->environment;
        } else {
            $env = Constants::DEFAULT_ENV;
        }

        if (!defined('APPLICATION_ENV')) {
            define('APPLICATION_ENV', $env);
        }

        $this->di->set('environment', function() use ($env) {
            return $env;
        }, true);
    }

    /**
     *
     */
    protected function initModules()
    {
        //registers sub modules if defined in configuration
        $subModuleManager = new SubModuleManager();
        if (isset($this->config->application->subModules)) {
            foreach ($this->config->application->subModules->toArray() as $subModuleName) {
                $subModuleManager->registerSubModule($subModuleName);
            }
        }

        //registers modules defined in modules.php file
        $modulesFile = $this->config->application->configDir . 'modules.php';
        if (!file_exists($modulesFile) || $this->di->get('environment') != Constants::DEFAULT_ENV) {
            ModuleLoader::dump($this->di);
        }
        $this->console->registerModules(require $modulesFile);

        $namespaces = array();
        //prepares modules configurations and modules task namespace
        foreach ($this->console->getModules() as $moduleName => $module) {
            $moduleConfigFile = dirname($module['path']) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
            if (file_exists($moduleConfigFile)) {
                $this->config->merge(require $moduleConfigFile);
            }

            $namespaces[$moduleName . '\Tasks'] = dirname($module['path']) . DIRECTORY_SEPARATOR . 'tasks';
        }

        //registers module's tasks directories
        $loader = new \Phalcon\Loader();
        $loader->registerNamespaces($namespaces, true);
        $loader->register();

        $this->di->set('modules', function() {
            return $this->console->getModules();
        });
    }

    /**
     * Initializes services
     */
    protected function initServices()
    {
        ServiceProviderLoader::autoload($this->di);
    }


    /**
     * Sets command line arguments
     *
     * @param $args
     */
    public function setArguments($args)
    {
        $this->arguments = $args;
    }

    /**
     * Setups CLI events manager
     */
    protected function initEventsManager()
    {
        //extracts default events manager
        $eventsManager = $this->di->getShared('eventsManager');
        //attaches new event console:beforeTaskHandle and console:afterTaskHandle
        $eventsManager->attach('console:beforeHandleTask', \Vegas\Cli\EventsListener\TaskListener::beforeHandleTask($this->arguments));
        $eventsManager->attach('console:afterHandleTask', \Vegas\Cli\EventsListener\TaskListener::afterHandleTask());
        $this->console->setEventsManager($eventsManager);
    }

    /**
     * {@inheritdoc}
     */
    public function setup()
    {
        $this->di->set('config', $this->config);

        $this->initEnvironment();
        $this->initLoader();
        $this->initModules();
        $this->initServices();
        $this->initEventsManager();

        $this->console->setDI($this->di);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $argumentParser = new Loader();
        $arguments = $argumentParser->parseArguments($this->console, $this->arguments);

        $this->console->handle($arguments);
    }
}
 