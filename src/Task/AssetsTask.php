<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Task;

use Vegas\Cli\Task\Option;

class AssetsTask extends \Vegas\Cli\Task
{
    public function publishAction()
    {
        echo "Copying assets..";

        $this->copyAllAssets();

        echo "\nDone.";
    }

    private function copyAllAssets()
    {
        $vegasCmfPath = APP_ROOT.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'vegas-cmf';
        $publicAssetsDir = $this->getOption('d', APP_ROOT.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'assets');

        $handle = opendir($vegasCmfPath);

        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry == "." || $entry == "..") {
                    continue;
                }

                $assetsDir = $vegasCmfPath.DIRECTORY_SEPARATOR.$entry.DIRECTORY_SEPARATOR.'assets';
                if (file_exists($assetsDir)) {
                    $this->copyRecursive($assetsDir, $publicAssetsDir);
                }
            }
            closedir($handle);
        }
    }

    private function copyRecursive($source, $dest, $permissions = 0755)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $this->copyRecursive("$source/$entry", "$dest/$entry");
        }

        // Clean up
        $dir->close();
        return true;
    }

    /**
     * Task must implement this method to set available options
     *
     * @return mixed
     */
    public function setOptions()
    {
        $action = new \Vegas\Cli\Task\Action('publish', 'Publish all assets');

        $dir = new Option('dir', 'd', 'Assets directory. Usage vegas:assets publish -d /path/to/assets');
        $action->addOption($dir);
        $this->addTaskAction($action);
    }
}