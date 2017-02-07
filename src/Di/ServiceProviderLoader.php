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
 
namespace Vegas\Di;

use Phalcon\Config;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\DiInterface;
use Phalcon\Loader;
use Vegas\Constants;
use Vegas\Util\FileWriter;

/**
 * Class ServiceProviderLoader
 * @package Vegas\Di
 */
class ServiceProviderLoader implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Name of file containing static list of services
     */
    const SERVICES_STATIC_FILE = 'services.php';

    /**
     * @param DiInterface $di
     */
    public function __construct(DiInterface $di)
    {
        $this->setDI($di);
    }

    /**
     * Dumps services to source file
     *
     * @param string $inputDirectory
     * @param string $outputDirectory
     * @return array
     */
    public function dump($inputDirectory, $outputDirectory)
    {
        $servicesList = array();

        //browses directory for searching service provider classes
        $directoryIterator = new \DirectoryIterator($inputDirectory);
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            $servicesList[$fileInfo->getBasename('.php')] = $fileInfo->getPathname();
        }

        //saves generated array to php source file
        FileWriter::writeObject(
            $outputDirectory . self::SERVICES_STATIC_FILE,
            $servicesList,
            true
        );

        ksort($servicesList);
        return $servicesList;
    }

    /**
     * Creates services autoloader
     *
     */
    public function autoload($inputDirectory, $outputDirectory)
    {
        if (!file_exists($outputDirectory . self::SERVICES_STATIC_FILE)
                || $this->di->get('environment') != Constants::DEFAULT_ENV) {
            $services = self::dump($inputDirectory, $outputDirectory);
        } else {
            $services = require($outputDirectory . self::SERVICES_STATIC_FILE);
        }

        self::setupServiceProvidersAutoloader($inputDirectory, $services);

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
            $servicesProviders[$serviceProviderName]->register($this->di);
        }
    }

    /**
     * Registers classes that contains services providers
     *
     * @param string $inputDirectory
     * @param array $services
     * @internal
     */
    private function setupServiceProvidersAutoloader($inputDirectory, array $services)
    {
        //creates the autoloader
        $loader = new Loader();

        //setup default path when is not defined
        foreach ($services as $className => $path) {
            if (!$path) {
                $services[$className] = $inputDirectory . sprintf('%s.php', $className);
            }
        }
        $loader->registerClasses($services, true);

        $loader->register();
    }
}
