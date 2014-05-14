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
 
namespace Vegas\Application;

use Phalcon\DI\FactoryDefault;
use Phalcon\DI;
use Phalcon\DiInterface;
use Vegas\BootstrapInterface;
use Vegas\DI\ServiceProviderLoader;
use Vegas\Mvc\Module\ModuleLoader;
use Vegas\Mvc\Module\SubModuleManager;

/**
 * Class Bootstrap
 * @package Vegas\Application
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @var DiInterface
     */
    protected $di;

    /**
     * @var \Vegas\Mvc\Application
     */
    protected $application;

    /**
     * @var \Phalcon\Config
     */
    protected $config;

    /**
     * @param \Phalcon\Config $config
     * @param \Phalcon\DI\FactoryDefault $di
     */
    public function __construct(\Phalcon\Config $config, FactoryDefault $di = null)
    {
        $this->config = $config;
        //the bootstrap DI can be overridden by already existing DI passed to constructor
        $this->di = ($di == null) ? new FactoryDefault() : $di;
        $this->application = new \Vegas\Mvc\Application();
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
                $this->config->application->pluginDir
            )
        )->register();
    }

    /**
     * Initializes application modules
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
        if (!file_exists($modulesFile)) {
            ModuleLoader::dump($this->di);
        }
        $this->application->registerModules(require $modulesFile);

        //prepares modules configurations
        foreach ($this->application->getModules() as $module) {
            $moduleConfigFile = dirname($module['path']) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
            if (file_exists($moduleConfigFile)) {
                $this->config->merge(require $moduleConfigFile);
            }
        }
    }

    /**
     * Initializes services
     */
    protected function initServices()
    {
        ServiceProviderLoader::autoload($this->di);
    }

    /**
     * Initializes routing
     */
    protected function initRoutes()
    {
        //setups router
        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard($this->di);
        $router = new \Vegas\Mvc\Router($routerAdapter);

        //adds routes defined in modules
        $modules = $this->application->getModules();
        foreach ($modules as $module) {
            $router->addModuleRoutes($module);
        }

        //adds routes defined in default file
        $defaultRoutesFile = $this->config->application->configDir . DIRECTORY_SEPARATOR . 'routes.php';
        if (file_exists($defaultRoutesFile)) {
            $router->addRoutes(require $defaultRoutesFile);
        }

        //setup router rules
        $router->setup();

        $this->di->set('router', $router->getRouter());
    }

    /**
     * Setups application
     *
     * @return $this
     */
    public function setup()
    {
        $this->di->set('config', $this->config);

        $this->initLoader();
        $this->initModules();
        $this->initRoutes();
        $this->initServices();

        $this->application->setDI($this->di);
        DI::setDefault($this->di);

        return $this;
    }

    /**
     * Start handling MVC requests
     *
     * @param null $uri
     * @return string
     */
    public function run($uri = null)
    {
        return $this->application->handle($uri)->getContent();
    }
} 