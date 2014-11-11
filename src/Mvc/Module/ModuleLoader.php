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

use Phalcon\DiInterface;
use Vegas\Util\FileWriter;

/**
 * Class ModuleLoader
 * @package Vegas\Mvc
 */
class ModuleLoader
{
    /**
     * Default name of file containing module settings
     */
    const MODULE_SETTINGS_FILE = 'Module.php';

    /**
     * Generates list of modules into source file
     *
     * @param DiInterface $di
     * @return array
     */
    public static function dump(DiInterface $di)
    {
        $modulesList = array();

        //extracts list of modules from module directory
        $config = $di->get('config');
        $directoryIterator = new \DirectoryIterator($config->application->moduleDir);
        foreach ($directoryIterator as $moduleDir) {
            if ($moduleDir->isDot()) continue;
            $moduleSettingsFile = $moduleDir->getPathname() . DIRECTORY_SEPARATOR . self::MODULE_SETTINGS_FILE;
            if (!file_exists($moduleSettingsFile)) {
                continue;
            }
            $modulesList[$moduleDir->getBasename()] = array(
                'className' =>  $moduleDir->getBasename() . '\\' . pathinfo(self::MODULE_SETTINGS_FILE, PATHINFO_FILENAME),
                'path'  =>  $moduleSettingsFile
            );
        }

        $modulesList = self::dumpModulesFromVendor($modulesList);

        //saves generated array to php source file
        FileWriter::write($config->application->configDir . 'modules.php', self::createFileContent($modulesList), true);

        return $modulesList;
    }

    /**
     * Extract Vegas modules from composer vegas-cmf vendors.
     *
     * @param $modulesList
     * @return mixed
     */
    private static function dumpModulesFromVendor(array $modulesList)
    {
        if (!file_exists(APP_ROOT.'/composer.json')) {
            return $modulesList;
        }

        $fileContent = file_get_contents(APP_ROOT.DIRECTORY_SEPARATOR.'composer.json');
        $json = json_decode($fileContent, true);

        $vendorDir = realpath(APP_ROOT.(isset($json['config']['vendor-dir']) ?
                DIRECTORY_SEPARATOR.$json['config']['vendor-dir'] :
                DIRECTORY_SEPARATOR.'vendor'));

        $vendorDir .= DIRECTORY_SEPARATOR.'vegas-cmf';
        $directoryIterator = new \DirectoryIterator($vendorDir);
        foreach ($directoryIterator as $libDir) {
            if ($libDir->isDot()) continue;
            $moduleSettingsFile = $vendorDir. DIRECTORY_SEPARATOR . $libDir . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR .self::MODULE_SETTINGS_FILE;
            if (!file_exists($moduleSettingsFile)) {
                continue;
            }

            $baseName = \Phalcon\Text::camelize($libDir->getBasename());
            if (!isset($modulesList[$baseName])) {
                $modulesList[$baseName] = array(
                    'className' =>  $baseName . '\\' . pathinfo(self::MODULE_SETTINGS_FILE, PATHINFO_FILENAME),
                    'path'  =>  $moduleSettingsFile
                );
            }
        }

        return $modulesList;
    }

    /**
     * Creates file's content with list of modules
     *
     * @param $modulesList
     * @return string
     * @internal
     */
    private static function createFileContent($modulesList)
    {
        return '<?php return ' . var_export($modulesList, true) . ';';
    }
} 