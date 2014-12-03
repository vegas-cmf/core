<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Application;

use Phalcon\DI\FactoryDefault;
use Phalcon\DI;
use Phalcon\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Vegas\BootstrapInterface;
use Vegas\Constants;
use Vegas\DI\ServiceProviderLoader;
use Vegas\Mvc\Dispatcher\Events\BeforeException;
use Vegas\Mvc\Module\Loader As ModuleLoader;

/**
 * Class Bootstrap
 *
 * Bootstraps mvc application
 *
 * @package Vegas\Application
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * Dependency injection
     *
     * @var DiInterface
     */
    protected $di;

    /**
     * MVC Application
     *
     * @var \Vegas\Mvc\Application
     */
    protected $application;

    /**
     * Application config
     *
     * @var \Phalcon\Config
     */
    protected $config;

    /**
     * Constructor
     * Initializes MVC Application
     * Initializes DI for Application
     *
     * @param \Phalcon\Config $config
     */
    public function __construct(\Phalcon\Config $config)
    {
        $this->config = $config;
        $this->di = new FactoryDefault();
        $this->application = new \Vegas\Mvc\Application();
    }

    /**
     * Sets Dependency Injector
     *
     * @param DiInterface $di
     */
    public function setDi(DiInterface $di)
    {
        $this->di = $di;
    }

    /**
     * Returns Dependency Injector
     *
     * @return FactoryDefault|DiInterface
     */
    public function getDI()
    {
        return $this->di;
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
        //registers modules defined in modules.php file
        $modulesFile = $this->config->application->configDir . 'modules.php';
        if (!file_exists($modulesFile) || $this->di->get('environment') != Constants::DEFAULT_ENV) {
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

        $this->di->set('modules', function() {
            return $this->application->getModules();
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
     * Initializes routing
     */
    protected function initRoutes()
    {
        //setups router
        $routerAdapter = new \Vegas\Mvc\Router\Adapter\Standard($this->di);
        $router = new \Vegas\Mvc\Router($this->di, $routerAdapter);

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
     * Registers default dispatcher
     *
     * @param $di
     */
    protected function initDispatcher()
    {
        $this->di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();

            $eventsManager = $this->di->getShared('eventsManager');
            $eventsManager->attach('dispatch:beforeException', BeforeException::getEvent());

            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        });
    }

    /**
     * Setups application
     *
     * @return $this
     */
    public function setup()
    {
        $this->di->set('config', $this->config);

        $this->initEnvironment();
        $this->initLoader();
        $this->initModules();
        $this->initRoutes();
        $this->initServices();
        $this->initDispatcher();

        $this->application->setDI($this->di);
        DI::setDefault($this->di);

        return $this;
    }

    /**
     * Start handling MVC requests
     *
     * @param string $uri
     * @return string
     */
    public function run($uri = null)
    {
        return $this->application->handle($uri)->getContent();
    }
} 