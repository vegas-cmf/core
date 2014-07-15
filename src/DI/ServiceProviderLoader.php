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
 
namespace Vegas\DI;

use Phalcon\DiInterface;
use Vegas\Constants;
use Vegas\Util\FileWriter;

/**
 * Class ServiceProviderLoader
 * @package Vegas\DI
 */
class ServiceProviderLoader
{
    /**
     * Dumps services to source file
     *
     * @param DiInterface $di
     * @return array
     */
    public static function dump(DiInterface $di)
    {
        $config = $di->get('config');
        $servicesList = array();

        //browses directory for searching service provider classes
        $directoryIterator = new \DirectoryIterator($config->application->serviceDir);
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            $servicesList[$fileInfo->getBasename('.php')] = $fileInfo->getPathname();
        }

        //saves generated array to php source file
        FileWriter::write($config->application->configDir . 'services.php', self::createFileContent($servicesList), true);

        ksort($servicesList);
        return $servicesList;
    }

    /**
     * @param $servicesList
     * @return string
     */
    private static function createFileContent($servicesList)
    {
        return '<?php return ' . var_export($servicesList, true) . ';';
    }

    /**
     * Creates services autoloader
     *
     * @param DiInterface $di
     */
    public static function autoload(DiInterface $di)
    {
        $config = $di->get('config');
        $configDir = $config->application->configDir;
        if (!file_exists($configDir . 'services.php') || $di->get('environment') != Constants::DEFAULT_ENV) {
            $services = self::dump($di);
        } else {
            $services = require($configDir . 'services.php');
        }

        self::setupServiceProvidersAutoloader($config, $services);

        //resolves services dependencies
        $dependencies = array();
        $servicesProviders = array();
        foreach ($services as $serviceProviderName => $path) {
            $reflectionClass = new \ReflectionClass($serviceProviderName);
            $serviceProviderInstance = $reflectionClass->newInstance();
            //fetches services dependencies
            $serviceDependencies = $serviceProviderInstance->getDependencies();

            //fetches name of service
            $serviceName = $reflectionClass->getConstant('SERVICE_NAME');

            //all services are in dependencies
            if (!isset($dependencies[$serviceName])) {
                $dependencies[$serviceName] = 0;
            }

            /**
             * Creates array of ordered dependencies
             */
            array_walk($serviceDependencies, function($dependency, $key) use (&$dependencies) {
                if (!isset($dependencies[$dependency])) {
                    $dependencies[$dependency] = 0;
                }
                $dependencies[$dependency]++;
            });
            $servicesProviders[$serviceName] = $serviceProviderInstance;
        }
        uasort($dependencies, function($a, $b) { return $b-$a; });

        //registers ordered dependencies
        foreach ($dependencies as $serviceProviderName => $dependency) {
            $servicesProviders[$serviceProviderName]->register($di);
        }
    }

    /**
     * Registers classes that contains services providers
     *
     * @param \Phalcon\Config $config
     * @param $services
     */
    private static function setupServiceProvidersAutoloader(\Phalcon\Config $config, $services)
    {
        //creates the autoloader
        $loader = new \Phalcon\Loader();

        //setup default path when is not defined
        foreach ($services as $className => $path) {
            if (!$path) {
                $services[$className] = $config->application->serviceDir . sprintf('%s.php', $className);
            }
        }
        $loader->registerClasses($services, true);

        $loader->register();
    }
}