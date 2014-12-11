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

namespace Vegas\Cli;

use Phalcon\Config;
use Phalcon\DI\FactoryDefault\CLI;
use Phalcon\DI\FactoryDefault;
use Phalcon\DiInterface;
use Phalcon\Loader as PhalconLoader;
use Vegas\Bootstrap\EnvironmentInitializerTrait;
use Vegas\Bootstrap\LoaderInitializerTrait;
use Vegas\Bootstrap\ModulesInitializerTrait;
use Vegas\Bootstrap\ServicesInitializerTrait;
use Vegas\BootstrapInterface;
use Vegas\Cli\EventsListener\TaskListener;
use Vegas\Cli\Exception as CliException;

/**
 * Class Bootstrap
 * @package Vegas\Cli
 */
class Bootstrap implements BootstrapInterface
{
    use ModulesInitializerTrait {
        initModules as baseInitModule;
    }

    use ServicesInitializerTrait;

    use EnvironmentInitializerTrait;

    use LoaderInitializerTrait;

    /**
     * Application arguments
     *
     * @var array
     * @internal
     * @internal
     */
    private $arguments;

    /**
     * Constructor
     * Initializes Console Application
     * Initializes DI for CLI application
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->di = new CLI();
        $this->application = new Console();
    }
    /**
     * Sets Dependency Injector
     *
     * @param DiInterface $di
     */
    public function setDI(DiInterface $di)
    {
        $this->di = $di;
    }

    /**
     * Returns Dependency Injector
     *
     * @return CLI|DiInterface
     */
    public function getDI()
    {
        return $this->di;
    }

    /**
     * @return Console
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Initializes application modules
     */
    protected function initModules(Config $config)
    {
        $this->baseInitModule($config);

        $namespaces = array();
        //prepares modules configurations and modules task namespace
        foreach ($this->application->getModules() as $moduleName => $module) {
            $namespaces[$moduleName . '\Tasks'] = dirname($module['path'])
                                                        . DIRECTORY_SEPARATOR . 'tasks';
        }

        //registers module's tasks directories
        $loader = new PhalconLoader();
        $loader->registerNamespaces($namespaces, true);
        $loader->register();
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
        $taskListener = new TaskListener();

        //extracts default events manager
        $eventsManager = $this->di->getShared('eventsManager');
        //attaches new event console:beforeTaskHandle and console:afterTaskHandle
        $eventsManager->attach(
            'console:beforeHandleTask', $taskListener->beforeHandleTask($this->arguments)
        );
        $eventsManager->attach(
            'console:afterHandleTask', $taskListener->afterHandleTask()
        );
        $this->application->setEventsManager($eventsManager);
    }

    /**
     * {@inheritdoc}
     */
    public function setup()
    {
        $this->di->set('config', $this->config);

        $this->initEnvironment($this->config);
        $this->initLoader($this->config);
        $this->initModules($this->config);
        $this->initServices($this->config);
        $this->initEventsManager();

        $this->application->setDI($this->di);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $argumentParser = new Loader();
        $arguments = $argumentParser->parseArguments($this->application, $this->arguments);

        $this->application->handle($arguments);
    }
}