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

namespace Vegas\Bootstrap;

use Phalcon\Config;
use Vegas\DI\ServiceProviderLoader;

trait ServicesInitializerTrait
{
    /**
     * Initializes services
     */
    public function initServices(Config $config)
    {
        $serviceProviderLoader = new ServiceProviderLoader($this->getDI());
        $serviceProviderLoader->autoload(
            $config->application->serviceDir,
            $config->application->configDir
        );
    }

    /**
     * @return mixed
     */
    abstract public function getApplication();

    /**
     * @return mixed
     */
    abstract public function getDI();
}
