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
 
namespace Vegas\DI;

use Phalcon\DiInterface;

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
     */
    public static function dump(DiInterface $di)
    {
        $config = $di->get('config');
        $servicesList = array();

        //browses directory for searching service provider classes
        $directoryIterator = new \DirectoryIterator($config->application->servicesDir);
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            $servicesList[$fileInfo->getBasename('.php')] = $fileInfo->getPathname();
        }

        //saves generated array to php source file
        file_put_contents(
            $config->application->configDir . 'services.php',
            '<?php return ' . var_export($servicesList, true) . ';'
        );
    }

    /**
     * Creates autoloader for services
     *
     * @param DiInterface $di
     */
    public static function autoload(DiInterface $di)
    {
        $config = $di->get('config');
        $configDir = $config->application->configDir;
        if (!file_exists($configDir . 'services.php')) {
            self::dump($config);
        }

        $services = require_once( $configDir . 'services.php' );
        self::setupServiceProvidersAutoloader($config, $services);

        foreach ($services as $serviceProviderName => $path) {
            $reflectionClass = new \ReflectionClass($serviceProviderName);
            $serviceProviderInstance = $reflectionClass->newInstance();
            $serviceProviderInstance->register($di);
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
                $services[$className] = $config->application->servicesDir . sprintf('%s.php', $className);
            }
        }
        $loader->registerClasses($services, true);

        $loader->register();
    }
}