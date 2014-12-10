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
use Phalcon\DiInterface;
use Vegas\BootstrapInterface;

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
     * Executes all bootstrap initialization methods
     * This method can be overloaded to load own initialization method.
     * @return mixed
     */
    public function setup()
    {
        // TODO: Implement setup() method.
    }

    /**
     * Runs application
     *
     * @return mixed
     */
    public function run()
    {
        // TODO: Implement run() method.
    }
}
 