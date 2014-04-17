<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Mvc;

class Application extends \Phalcon\Mvc\Application
{
    public function registerModules($modules, $merge = null)
    {
        $this->registerSharedData($modules);
        parent::registerModules($modules, $merge);
    }
    
    private function registerSharedData($modules)
    {
        $loader = new \Phalcon\Loader();
        
        foreach ($modules As $name => $module)
        {
            $loader->registerNamespaces(
                array(
                    $name.'\Models'   => dirname($module['path']).'/models/',
                    $name.'\Services' => dirname($module['path']).'/services/',
                ), true
            );
        }

        $loader->register();
    }
}
