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
 
namespace Vegas\Task;

use Phalcon\DI;
use Vegas\Cli\Task\Action;
use Vegas\Cli\Task;
use Vegas\Cli\TaskAbstract;

/**
 * Class CacheTask
 * @package Vegas\Task
 */
class CacheTask extends TaskAbstract
{
    /**
     * Cleans application cache
     */
    public function cleanAction()
    {
        $this->putText("Cleaning cache...");

        $di = DI::getDefault();

        if ($di->has('config')) {
            $config = $di->get('config');

            if (!empty($config->application->view->cacheDir)) {
                $this->removeFilesFromDir($config->application->view->cacheDir);
            }

            $this->putSuccess("Done.");
        }
    }

    /**
     * Removes files from cache dir
     *
     * @param $dir
     * @internal
     */
    private function removeFilesFromDir($dir)
    {
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry == "." || $entry == "..") {
                    continue;
                }

                if (!unlink($dir.DIRECTORY_SEPARATOR.$entry)) {
                    $this->putWarning("Can not remove: ".$dir.DIRECTORY_SEPARATOR.$entry);
                }
            }
            closedir($handle);
        }
    }

    /**
     * Task's available options
     *
     * @return mixed
     */
    public function setupOptions()
    {
        $action = new Action('clean', 'Clean cache');
        $this->addTaskAction($action);
    }
}