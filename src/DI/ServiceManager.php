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
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\DI;

use Vegas\DI\Service\Exception;

class ServiceManager implements \Phalcon\DI\InjectionAwareInterface
{
    use InjectionAwareTrait;

    public function get($name)
    {
        return $this->getService($name);
    }

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
    
    private function isRegisteredService($name)
    {
        if ($this->di->has($name)) {
            return true;
        }
        
        return false;
    }
    
    private function registerService($name)
    {
        $namespace = $this->translateNameToNamespace($name);
        $this->di->set($name, $namespace, true);
    }
    
    private function translateNameToNamespace($name)
    {
        $nameParts = array_map('ucfirst', explode(':',$name));
        $module = array_shift($nameParts);
        
        $namespace = $module.'\Services\\';
        $namespace.= implode('\\',$nameParts);

        return $namespace;
    }
}
