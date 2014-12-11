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

namespace Vegas\Bootstrap;

use Phalcon\Config;
use Phalcon\DI\FactoryDefault;
use Phalcon\DI;
use Phalcon\Mvc\Dispatcher;
use Vegas\Constants;
use Vegas\Mvc\Module\Exception\InvalidModulesListException;
use Vegas\Mvc\Module\Loader as ModuleLoader;
use Vegas\Mvc\Module\Loader;
use Vegas\Mvc\Router;

trait ModulesInitializerTrait
{
    /**
     * Initializes application modules
     */
    protected function initModules(Config $config)
    {
        $moduleLoader = new ModuleLoader($this->getDI());
        //registers modules defined in modules.php file
        $modulesFile = $config->application->configDir
            . Loader::MODULE_STATIC_FILE;
        /**
         * For non-default environment modules are being dumped in each application start
         */
        if (!file_exists($modulesFile) || $this->getDI()->get('environment') != Constants::DEFAULT_ENV) {
            $modules = $moduleLoader->dump(
                $config->application->moduleDir,
                $config->application->configDir
            );
        } else {
            $modules = require $modulesFile;
        }
        if (!is_array($modules)) {
            throw new InvalidModulesListException();
        }
        $this->getApplication()->registerModules($modules);

        //prepares modules configurations
        foreach ($this->getApplication()->getModules() as $module) {
            $moduleConfigFile = dirname($module['path'])
                . DIRECTORY_SEPARATOR
                . 'config'
                . DIRECTORY_SEPARATOR
                . 'config.php';
            if (file_exists($moduleConfigFile)) {
                $moduleConfig = require $moduleConfigFile;
                if (is_array($moduleConfig)) {
                    $config->merge($moduleConfig);
                }
            }
        }

        $this->getDI()->set('modules', function() {
            return $this->getApplication()->getModules();
        });
    }

    /**
     * @return mixed
     */
    abstract public function getDI();

    /**
     * @return mixed
     */
    abstract public function getApplication();
}
 