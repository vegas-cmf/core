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
 
namespace Vegas\Db\Decorator\Helper;

/**
 * Class MappingHelperTrait
 * Strictly coupled with \Vegas\Db\MappingResolverTrait
 * Manages mappings specified manually in models & calls the mapping resolving tasks.
 * @package Vegas\Db\Decorator
 */
trait MappingHelperTrait
{
    /**
     * Defines field mappings in format
     *
     * <code>
     * [
     *      'field_1' => 'mapper_1',
     *      'field_2' => ['mapper_1', 'mapper_2']
     * ]
     * </code>
     * @var array
     */
    protected $mappings = [];

    /**
     * Returns array of attributes with mapped values
     *
     * @return array
     */
    public function toMappedArray()
    {
        $values = $this->toArray();
        $mappedValues = [];
        foreach ($values as $key => $value) {
            $mappedValues[$key] = $this->readMapped($key);
        }

        return $mappedValues;
    }

    /**
     * Returns mapped value of attribute
     *
     * @param string $name
     * @return mixed
     */
    public function readMapped($name)
    {
        if (!$this->hasMapping($name) && isset($this->mappings[$name])) {
            $this->addMapping($name, $this->mappings[$name]);
        }
        $value = $this->readAttribute($name);

        $value = $this->resolveMapping($name, $value);

        return $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    abstract public function hasMapping($name);

    /**
     * @param string $name
     * @param mixed $mappings
     * @return mixed
     */
    abstract public function addMapping($name, $mappings);

    /**
     * @param string $name
     * @return mixed
     */
    abstract public function readAttribute($name);

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    abstract public function resolveMapping($name, $value);
} 