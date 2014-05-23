<?php
/**
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * Date: 4/23/14
 * Time: 2:14 PM
 */

namespace Vegas\Db;

/**
 * Class HasMappingTrait
 * @package Vegas\Db
 */
trait HasMappingTrait
{
    /**
     * List of attached mappings
     *
     * @var array
     */
    private $mappingsContainer = array();

    /**
     * Adds mapping for indicated attribute
     *
     * @param $attributeName
     * @param $mappings
     * @return $this
     */
    public function addMapping($attributeName, $mappings)
    {
        if (!is_array($mappings)) {
            $mappings = array($mappings);
        }
        if (!$this->hasMapping($attributeName)) {
            $this->mappingsContainer[$attributeName] = array();
        }
        foreach ($mappings as $mapping) {
            $this->mappingsContainer[$attributeName][] = $mapping;
        }

        return $this;
    }

    /**
     * Removes mapping from indicated attribute
     *
     * @param $attributeName
     * @return $this
     */
    public function removeMapping($attributeName)
    {
        if ($this->hasMapping($attributeName)) {
            unset($this->mappingsContainer[$attributeName]);
        }

        return $this;
    }

    /**
     * Determines if attribute has defined mapping
     *
     * @param $attributeName
     * @return bool
     */
    public function hasMapping($attributeName)
    {
        return array_key_exists($attributeName, $this->mappingsContainer);
    }

    /**
     * Clear mappings
     *
     * @return $this
     */
    public function clearMappings()
    {
        $this->mappingsContainer = array();
        return $this;
    }

    /**
     * Filter value of indicated attribute by applying defined mappings
     *
     * @param $attributeName
     * @param $value
     * @return mixed
     */
    public function resolveMapping($attributeName, $value)
    {
        //when no mappings was defined, returns raw value
        if (!$this->hasMapping($attributeName)) {
            return $value;
        }

        $mappings = $this->mappingsContainer[$attributeName];
        foreach ($mappings as $mapping) {
            //get mapping instance from mapping manager
            $mappingResolver = MappingManager::find($mapping);
            $mappingResolver->resolve($value);
        }

        return $value;
    }
}