<?php
/**
 * This file is part of Vegas package
 *
 * ServiceManager share and group services from all modules required for current
 * context. To load Foo\Services\BarBaz call:
 * <code>
 * $di->get('serviceManager')->getService('foo:barBaz');
 * // or in controller 
 * $this->serviceManager->getService('foo:barBaz');
 * // or in view
 * {{ serviceManager.getService('foo:barBaz') }}
 * </code>
 * 
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\DI;

use Phalcon\DI\InjectionAwareInterface;
use Vegas\DI\Service\Exception;

/**
 * Class ServiceManager
 * @package Vegas\DI
 */
class ServiceManager implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Alias for getService.
     *
     * @param $name
     * @return object
     */
    public function get($name)
    {
        return $this->getService($name);
    }

    /**
     * Try to register and return service.
     *
     * @param $name
     * @return object
     * @throws Service\Exception
     */
    public function getService($name)
    {
        try {
            if (!$this->isRegisteredService($name)) {
                $this->registerService($name);
            }
            return $this->di->get($name);
        } catch (\Phalcon\DI\Exception $ex) {
            throw new Exception($ex->getMessage().', using: '.$name);
        }
    }

    /**
     * Alias for hasService.
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->hasService($name);
    }

    /**
     * Try to register service and return information about service existence.
     *
     * @param $name
     * @return bool
     */
    public function hasService($name)
    {
        try {
            $service = $this->getService($name);
            return !empty($service);
        } catch (\Phalcon\DI\Exception $ex) {
            return false;
        }
    }

    /**
     * @param $name
     * @return bool
     * @internal
     */
    private function isRegisteredService($name)
    {
        if ($this->di->has($name)) {
            return true;
        }
        
        return false;
    }

    /**
     * @param $name
     * @internal
     */
    private function registerService($name)
    {
        $namespace = $this->translateNameToNamespace($name);
        $this->di->set($name, $namespace, true);
    }

    /**
     * @param $name
     * @return string
     * @internal
     */
    private function translateNameToNamespace($name)
    {
        $nameParts = array_map('ucfirst', explode(':',$name));
        $module = array_shift($nameParts);
        
        $namespace = $module.'\Services\\';
        $namespace.= implode('\\',$nameParts);

        return $namespace;
    }
}
