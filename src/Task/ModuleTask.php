<?php
/**
 * This file is part of Vegas package
 *
 * @author Radosław Fąfara <radek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Task;

use Phalcon\Di;
use Vegas\Cli\Task\Action;
use Vegas\Cli\Task;
use Vegas\Cli\TaskAbstract;

/**
 * Class ModuleTask
 * @package Vegas\Task
 */
class ModuleTask extends TaskAbstract
{
    /**
     * Dumps modules & services files used for application autoloading
     */
    public function dumpAction()
    {
        $this->putText("Dumping modules & services files...");

        if ($this->isConfigured()) {
            $config = $this->getDI()->get('config');

            $moduleLoader = new \Vegas\Mvc\Module\Loader($this->getDI());
            $moduleLoader->dump(
                $config->application->moduleDir,
                $config->application->configDir
            );

            $serviceProviderLoader = new \Vegas\Di\ServiceProviderLoader($this->getDI());
            $serviceProviderLoader->autoload(
                $config->application->serviceDir,
                $config->application->configDir
            );

            $this->putSuccess("Done.");
        }
    }

    /**
     * @return bool
     */
    private function isConfigured()
    {
        if (!$this->getDI()->has('config')) {
            return false;
        }
        $config = $this->getDI()->get('config');
        return !empty($config->application)
            && !empty($config->application->moduleDir)
            && !empty($config->application->serviceDir)
            && !empty($config->application->configDir);
    }

    /**
     * Task's available options
     *
     * @return mixed
     */
    public function setupOptions()
    {
        $action = new Action('dump', 'Dump modules & services files');
        $this->addTaskAction($action);
    }
}
