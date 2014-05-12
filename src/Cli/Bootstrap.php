<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Cli;

use Phalcon\CLI\Console;
use Phalcon\DI\FactoryDefault\CLI;
use Vegas\BootstrapInterface;
use Vegas\Mvc\Module\ModuleLoader;

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

    protected function initModules()
    {
        //registers modules defined in modules.php file
        $modulesFile = $this->config->application->configDir . 'modules.php';
        if (!file_exists($modulesFile)) {
            ModuleLoader::dump($this->di);
        }
        $this->console->registerModules(require $modulesFile);

        $dirs = array();
        //prepares modules configurations
        foreach ($this->console->getModules() as $module) {
            $moduleConfigFile = dirname($module['path']) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
            if (file_exists($moduleConfigFile)) {
                $this->config->merge(require $moduleConfigFile);
            }

            $dirs[] = dirname($module['path']) . DIRECTORY_SEPARATOR . 'tasks';
        }

        //registers module's tasks directories
        $loader = new \Phalcon\Loader();
        $loader->registerDirs($dirs);
        $loader->register();
    }

    public function setArguments($args)
    {
        $this->arguments = $args;
    }

    public function setup()
    {
        $this->initLoader();
        $this->initModules();

        $this->console->setDI($this->di);
        return $this;
    }

    public function run()
    {
        $arguments = Dispatcher::autoload($this->console, $this->arguments);
        $this->console->handle($arguments);
    }
}
 