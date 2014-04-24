<?php
/**
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * Date: 4/23/14
 * Time: 2:14 PM
 */

namespace Vegas\Db;

use Vegas\Db\MappingInterface;

trait HasMappingTrait
{
    private $mappingsContainer = array();

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

    public function removeMapping($attributeName)
    {
        if ($this->hasMapping($attributeName)) {
            unset($this->mappingsContainer[$attributeName]);
        }

        return $this;
    }

    public function hasMapping($attributeName)
    {
        return array_key_exists($attributeName, $this->mappingsContainer);
    }

    public function clearMappings()
    {
        $this->mappingsContainer = array();
        return $this;
    }

    public function resolveMapping($attributeName, $value)
    {
        if (!$this->hasMapping($attributeName)) {
            return $value;
        }

        $mappings = $this->mappingsContainer[$attributeName];
        foreach ($mappings as $mapping) {
            $mappingResolver = MappingManager::find($mapping);
            $mappingResolver->resolve($value);
        }

        return $value;
    }
}