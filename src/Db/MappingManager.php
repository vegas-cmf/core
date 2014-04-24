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
 
namespace Vegas\Db;

use Vegas\Db\Exception\InvalidMappingClassException;

class MappingManager
{
    private static $container = array();

    public function add($mappingClass)
    {
        try {
            $reflectionClass = new \ReflectionClass($mappingClass);
            $mappingClassInstance = $reflectionClass->newInstance();
            if (!$mappingClassInstance instanceof MappingInterface) {
                throw new InvalidMappingClassException('Mapping class must be an instance of MappingInterface');
            }
            self::$container[$mappingClassInstance->getName()] = $mappingClassInstance;
        } catch (\Exception $ex) {
            throw new InvalidMappingClassException($ex->getMessage());
        }
        return $this;
    }

    public function remove($mappingName)
    {
        unset(self::$container[$mappingName]);
        return $this;
    }

    public static function find($mappingName)
    {
        if (!array_key_exists($mappingName, self::$container)) {
            return false;
        }

        return self::$container[$mappingName];
    }
} 