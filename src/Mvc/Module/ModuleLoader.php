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
                'className' =>  $moduleDir->getBasename() . '\\' . self::MODULE_SETTINGS_FILE,
                'path'  =>  $moduleSettingsFile
            );
        }

        //saves generated array to php source file
        file_put_contents(
            $config->application->configDir . 'modules.php',
            '<?php return ' . var_export($modulesList, true) . ';'
        );
    }
} 