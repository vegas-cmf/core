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
 
namespace Vegas\Mvc\Module;

use Phalcon\DiInterface;

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

        //creates path to modules.php file
        $modulesFilePath = $config->application->configDir . 'modules.php';
        //prepares string content for modules.php file
        $modulesListStr = self::createFileContent($modulesList);

        //compares current modules.php content with new modules array
        //when file content are equal, then don't create a new modules file
        if (file_exists($modulesFilePath)) {
            $currentContent = file_get_contents($modulesFilePath);
            if (strcmp($currentContent, $modulesListStr) === 0) {
                return $modulesList;
            }
        }

        //saves generated array to php source file
        file_put_contents(
            $modulesFilePath,
            $modulesListStr
        );

        return $modulesList;
    }

    /**
     * @param $modulesList
     * @return string
     */
    private static function createFileContent($modulesList)
    {
        return '<?php return ' . var_export($modulesList, true) . ';';
    }
} 