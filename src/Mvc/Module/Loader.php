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
use Phalcon\Text;
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
     * Dependency injector
     *
     * @var DiInterface $dependencyInjector
     */
    protected $di;

    /**
     * @param DiInterface $di
     */
    public function __construct(DiInterface $di)
    {
        $this->di = $di;
    }

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
    public function dumpModulesFromVendor(array &$modulesList)
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

        foreach ($this->getModuleProviders() as $provider) {
            $providerDir = $vendorDir . DIRECTORY_SEPARATOR . $provider;
            $this->dumpSingleProviderModulesFromVendor($modulesList, $providerDir);
        }

        return $modulesList;
    }

    /**
     * Extracts Vegas modules from specific provider in vendor directory.
     *
     * @param array $modulesList
     * @param string $providerDir directory path for module provider (e.x. vegas-cmf)
     */
    private function dumpSingleProviderModulesFromVendor(array &$modulesList, $providerDir)
    {
        $directoryIterator = new \DirectoryIterator($providerDir);
        foreach ($directoryIterator as $libDir) {
            if ($libDir->isDot()) {
                continue;
            }
            //creates path to Module.php file
            $moduleSettingsFile = implode(DIRECTORY_SEPARATOR, [
                $providerDir, $libDir, 'module', self::MODULE_SETTINGS_FILE
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
    }

    /**
     * Get available module providers - default is vegas-cmf.
     * Additional providers can be placed under application->vendorModuleProvider key in config file.
     * Value of the key can be either a string with name or an array with multiple names.
     *
     * @return array
     */
    private function getModuleProviders()
    {
        $defaultProviders = [
            'vegas-cmf'
        ];

        $appConfig = $this->di->get('config')->application;
        if (!isset($appConfig->vendorModuleProvider)) {
            $providerNames = [];
        } else if (is_string($appConfig->vendorModuleProvider)) {
            $providerNames = [$appConfig->vendorModuleProvider];
        } else {
            $providerNames = $appConfig->vendorModuleProvider->toArray();
        }

        return array_unique(array_merge($defaultProviders, $providerNames));
    }
}
