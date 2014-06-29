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
 
namespace Vegas\Mvc\Module;

use Phalcon\DI;
use Phalcon\Loader;

/**
 * Class SubModuleManager
 *
 * Simple sub-modules manager
 *
 * @package Vegas\Mvc\Module
 */
class SubModuleManager
{
    /**
     * List of registered sub-modules
     *
     * @var array
     */
    private static $subModulesContainer = array();

    /**
     * Registers sub-module determined by string-name
     *
     * @param $subModuleName
     */
    public function registerSubModule($subModuleName)
    {
        self::$subModulesContainer[] = $subModuleName;
    }

    /**
     * Returns registered sub-modules
     *
     * @return array
     */
    public static function getSubModules()
    {
        return self::$subModulesContainer;
    }

    /**
     * Determines if sub-module was registered
     *
     * @param $subModuleName
     * @return bool
     */
    public static function isRegistered($subModuleName)
    {
        return in_array($subModuleName, self::$subModulesContainer);
    }
}