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
 
namespace Vegas\Db;

use Vegas\Db\Exception\InvalidMappingClassException;
use Vegas\Db\Exception\MappingClassNotFoundException;

/**
 * Class MappingManager
 * @package Vegas\Db
 */
class MappingManager
{
    /**
     * Registered mappers
     *
     * @var array
     * @internal
     */
    private static $container = array();

    /**
     * Adds new mapping class
     *
     * @param mixed $mappingClass     Object or full class name is acceptable
     * @return $this
     * @throws Exception\InvalidMappingClassException
     */
    public function add($mappingClass)
    {
        try {
            if ($mappingClass instanceof MappingInterface) {
                $mappingClassInstance = $mappingClass;
            } else {
                $reflectionClass = new \ReflectionClass($mappingClass);
                $mappingClassInstance = $reflectionClass->newInstance();
                if (!$mappingClassInstance instanceof MappingInterface) {
                    throw new InvalidMappingClassException('Mapping class must be an instance of MappingInterface');
                }
            }
            self::$container[$mappingClassInstance->getName()] = $mappingClassInstance;
        } catch (\Exception $ex) {
            throw new InvalidMappingClassException($ex->getMessage());
        }
        return $this;
    }

    /**
     * Removes mapper indicated by name
     *
     * @param $mappingName
     * @return $this
     */
    public function remove($mappingName)
    {
        unset(self::$container[$mappingName]);
        return $this;
    }

    /**
     * Finds the mapper by its name
     *
     * @param $mappingName
     * @throws Exception\MappingClassNotFoundException
     * @return bool
     */
    public static function find($mappingName)
    {
        if (!array_key_exists($mappingName, self::$container)) {
            throw new MappingClassNotFoundException($mappingName);
        }

        return self::$container[$mappingName];
    }

    /**
     * Returns list of available mappers
     *
     * @return array
     */
    public function getMappers()
    {
        return self::$container;
    }
} 