<?php
/**
 * @author Sławomir Żytko <slawek@amsterdam-standard.pl>
 * @copyright (c) 2014, Amsterdam Standard
 */

namespace Vegas\Bootstrap;

use Phalcon\Config;
use Vegas\Constants;

trait EnvironmentInitializerTrait
{

    /**
     * Initializes application environment
     */
    protected function initEnvironment(Config $config)
    {
        if (isset($config->application->environment)) {
            $env = $config->application->environment;
        } else {
            $env = Constants::DEFAULT_ENV;
        }

        if (!defined('APPLICATION_ENV')) {
            define('APPLICATION_ENV', $env);
        }

        $this->getDI()->set('environment', function() use ($env) {
            return $env;
        }, true);
    }

    /**
     * @return mixed
     */
    abstract public function getDI();
}
 