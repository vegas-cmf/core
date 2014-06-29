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
 
namespace Vegas\Task;

use Phalcon\DI;

class CacheTask extends \Vegas\Cli\Task
{
    public function cleanAction()
    {
        echo "Cleaning cache..";

        $di = DI::getDefault();

        if ($di->has('config')) {
            $config = $di->get('config');

            if (!empty($config->application->view->cacheDir)) {
                $this->removeFilesFromDir($config->application->view->cacheDir);
            }

            echo "\nDone.";
        }
    }

    private function removeFilesFromDir($dir)
    {
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry == "." || $entry == "..") {
                    continue;
                }

                if (!unlink($dir.DIRECTORY_SEPARATOR.$entry)) {
                    echo "\nCan not remove: ".$dir.DIRECTORY_SEPARATOR.$entry;
                }
            }
            closedir($handle);
        }
    }

    /**
     * Task must implement this method to set available options
     *
     * @return mixed
     */
    public function setOptions()
    {
        // TODO: Implement setOptions() method.
    }
}