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

namespace Vegas\Mvc;

use Phalcon\Config;
use Phalcon\DI\FactoryDefault;
use Phalcon\DI;
use Phalcon\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Vegas\Bootstrap\EnvironmentInitializerTrait;
use Vegas\Bootstrap\ErrorHandlerInitializerTrait;
use Vegas\Bootstrap\LoaderInitializerTrait;
use Vegas\Bootstrap\ModulesInitializerTrait;
use Vegas\Bootstrap\RoutesInitializerTrait;
use Vegas\Bootstrap\ServicesInitializerTrait;
use Vegas\BootstrapInterface;
use Vegas\Mvc\Dispatcher\Events\ExceptionListener;
use Vegas\Mvc\Router;

class Bootstrap implements BootstrapInterface
{
    use ModulesInitializerTrait;

    use ServicesInitializerTrait;

    use RoutesInitializerTrait;

    use EnvironmentInitializerTrait;

    use ErrorHandlerInitializerTrait;

    use LoaderInitializerTrait;

    /**
     * Dependency injection
     *
     * @var DiInterface
     */
    protected $di;

    /**
     * MVC Application
     *
     * @var Application
     */
    protected $application;

    /**
     * Application config
     *
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     * Initializes MVC Application
     * Initializes DI for Application
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->di = new FactoryDefault();
        $this->application = new Application();
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
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Registers default dispatcher
     */
    public function initDispatcher()
    {
        $this->di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();

            /**
             * @var \Phalcon\Events\Manager $eventsManager
             */
            $eventsManager = $this->di->getShared('eventsManager');
            $eventsManager->attach(
                'dispatch:beforeException',
                (new ExceptionListener())->beforeException()
            );

            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        });
    }

    /**
     * Executes all bootstrap initialization methods
     * This method can be overloaded to load own initialization method.
     * @return mixed
     */
    public function setup()
    {
        $this->di->set('config', $this->config);

        $this->initEnvironment($this->config);
        $this->initErrorHandler($this->config);
        $this->initLoader($this->config);
        $this->initModules($this->config);
        $this->initRoutes($this->config);
        $this->initServices($this->config);
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
        return $this->application
            ->handle($uri)
            ->getContent();
    }
}
