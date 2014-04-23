<?php
/**
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * Date: 4/23/14
 * Time: 2:14 PM
 */

namespace Vegas\Db\Mapping;


use Vegas\Db\MappingInterface;

trait HasMappingTrait
{
    protected $mappings = array();

    public function addMapping(MappingInterface $mapping)
    {
        $this->mappings[$mapping->getName()] = $mapping;
        return $this;
    }

    public function removeMapping($mappingName)
    {
        if (array_key_exists($mappingName, $this->mappings)) {
            unset($this->mappings[$mappingName]);
        }

        return $this;
    }

    public function clearMappings()
    {
        $this->mappings = array();
        return $this;
    }
}