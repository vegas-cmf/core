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

/**
 * Class Application
 * @package Vegas\Mvc
 */
class Application extends \Phalcon\Mvc\Application
{
    /**
     * Register an array of modules present in the application
     *
     *<code>
     *	$this->registerModules(array(
     *		'frontend' => array(
     *			'className' => 'Multiple\Frontend\Module',
     *			'path' => '../apps/frontend/Module.php'
     *		),
     *		'backend' => array(
     *			'className' => 'Multiple\Backend\Module',
     *			'path' => '../apps/backend/Module.php'
     *		)
     *	));
     *</code>
     *
     * @param array $modules
     * @param boolean $merge
     */
    public function registerModules($modules, $merge = null)
    {
        $this->registerSharedData($modules);
        parent::registerModules($modules, $merge);
    }

    /**
     * Registers namespaces for models and services within modules
     *
     * @param $modules
     */
    private function registerSharedData($modules)
    {
        $loader = new \Phalcon\Loader();
        
        foreach ($modules As $name => $module)
        {
            $loader->registerNamespaces(
                array(
                    $name.'\Models'   => dirname($module['path']).'/models/',
                    $name.'\Services' => dirname($module['path']).'/services/',
                    $name.'\Components' => dirname($module['path']).'/components/',
                ), true
            );
        }

        $loader->register();
    }
}