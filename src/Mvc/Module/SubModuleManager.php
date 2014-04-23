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
 
namespace Vegas\Mvc\Module;

use Phalcon\DI;
use Phalcon\Loader;

class SubModuleManager
{
    private static $subModulesContainer = array();

    public function registerSubModule($subModuleName)
    {
        self::$subModulesContainer[] = $subModuleName;
    }

    public static function getSubModules()
    {
        return self::$subModulesContainer;
    }

    public static function isRegistered($subModuleName)
    {
        return in_array($subModuleName, self::$subModulesContainer);
    }
}