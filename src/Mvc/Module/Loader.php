<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl> Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Mvc\Module;

use Phalcon\Text;
use Vegas\DI\InjectionAwareTrait;
use Vegas\Util\FileWriter;

/**
 * Class Loader
 * @package Vegas\Mvc\Module
 */
class Loader
{
    /**
     * Default name of file containing module settings
     */
    const MODULE_SETTINGS_FILE = 'Module.php';

    /**
     * Name of file containing list of modules
     */
    const MODULE_STATIC_FILE = 'modules.php';

    /**
     * Generates list of modules into source file
     *
     * @param string $inputDirectory
     * @param string $outputDirectory
     * @param bool $dumpVendorModules
     * @return array
     */
    public function dump($inputDirectory, $outputDirectory, $dumpVendorModules = true)
    {
        $modulesList = [];

        //extracts list of modules from module directory
        $directoryIterator = new \DirectoryIterator($inputDirectory);
        foreach ($directoryIterator as $moduleDir) {
            if ($moduleDir->isDot()) {
                continue;
            }

            $moduleSettingsFile = $moduleDir->getPathname()
                                        . DIRECTORY_SEPARATOR
                                        . self::MODULE_SETTINGS_FILE;
            if (!file_exists($moduleSettingsFile)) {
                continue;
            }
            $modulesList[$moduleDir->getBasename()] = [
                'className' =>  $moduleDir->getBasename()
                                        . '\\'
                                        . pathinfo(self::MODULE_SETTINGS_FILE, PATHINFO_FILENAME),
                'path'  =>  $moduleSettingsFile
            ];
        }

        if ($dumpVendorModules) {
            $this->dumpModulesFromVendor($modulesList);
        }

        //saves generated array to php source file
        FileWriter::writeObject(
            $outputDirectory . self::MODULE_STATIC_FILE,
            $modulesList,
            true
        );

        return $modulesList;
    }

    /**
     * Extract Vegas modules from composer vegas-cmf vendors.
     *
     * @param $modulesList
     * @return mixed
     */
    private function dumpModulesFromVendor(array &$modulesList)
    {
        if (!file_exists(APP_ROOT.'/composer.json')) {
            return $modulesList;
        }

        $fileContent = file_get_contents(APP_ROOT . DIRECTORY_SEPARATOR . 'composer.json');
        $json = json_decode($fileContent, true);

        $vendorDir = realpath(APP_ROOT .
            (
                isset($json['config']['vendor-dir'])
                    ? DIRECTORY_SEPARATOR . $json['config']['vendor-dir']
                    : DIRECTORY_SEPARATOR.'vendor'
            )
        );

        $vendorDir .= DIRECTORY_SEPARATOR . 'vegas-cmf';
        $directoryIterator = new \DirectoryIterator($vendorDir);
        foreach ($directoryIterator as $libDir) {
            if ($libDir->isDot()) {
                continue;
            }
            //creates path to Module.php file
            $moduleSettingsFile = implode(DIRECTORY_SEPARATOR, [
                $vendorDir, $libDir, 'module', self::MODULE_SETTINGS_FILE
            ]);

            if (!file_exists($moduleSettingsFile)) {
                continue;
            }

            $baseName = Text::camelize($libDir->getBasename());
            if (!isset($modulesList[$baseName])) {
                $modulesList[$baseName] = [
                    'className' =>  $baseName
                                        . '\\'
                                        . pathinfo(self::MODULE_SETTINGS_FILE, PATHINFO_FILENAME),
                    'path'  =>  $moduleSettingsFile
                ];
            }
        }

        return $modulesList;
    }
} 