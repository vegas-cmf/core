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


namespace Vegas\Db;

/**
 * Class MappingResolverTrait
 * @package Vegas\Db
 */
trait MappingResolverTrait
{
    /**
     * List of attached mappings
     *
     * @var array
     * @internal
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
        if ($this->mappingsContainer[$attributeName] !== null) {
            foreach ($mappings as $mapping) {
                $this->mappingsContainer[$attributeName][] = $mapping;
            }
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
            $this->mappingsContainer[$attributeName] = null;
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
        foreach ($this->mappingsContainer as $attributeName => $mapping) {
            $this->mappingsContainer[$attributeName] = null;
        }
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
        if (is_array($mappings)) {
            foreach ($mappings as $mappingResolver) {
                if (!$mappingResolver instanceof MappingInterface) {
                    //get mapping instance from mapping manager
                    $mappingResolver = MappingManager::find($mappingResolver);
                }
                $mappingResolver->resolve($value);
            }
        }

        return $value;
    }
}