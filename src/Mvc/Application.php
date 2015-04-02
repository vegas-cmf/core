<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Mvc;
use Phalcon\Loader;

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
     *		'Example' => array(
     *			'className' => 'Example\\Module',
     *			'path' => APP_ROOT . '/app/modules/Example/Module.php'
     *		),
     *		'Test' => array(
     *			'className' => 'Test\\Module',
     *			'path' => APP_ROOT . '/app/modules/Test/Module.php'
     *		)
     *	));
     *</code>
     *
     * @param array $modules
     * @param boolean $merge
     */
    public function registerModules(array $modules, $merge = null)
    {
        $this->registerSharedData($modules);
        parent::registerModules($modules, $merge);
    }

    /**
     * Registers namespaces for models and services within modules
     *
     * @param array $modules
     * @internal
     */
    private function registerSharedData(array $modules)
    {
        $loader = new Loader();
        
        foreach ($modules As $name => $module)
        {
            $loader->registerNamespaces(
                array(
                    $name.'\Forms'   => dirname($module['path']).'/forms/',
                    $name.'\Models'   => dirname($module['path']).'/models/',
                    $name.'\Services' => dirname($module['path']).'/services/',
                    $name.'\Components' => dirname($module['path']).'/components/',
                ), true
            );
        }

        $loader->register();
    }
}
