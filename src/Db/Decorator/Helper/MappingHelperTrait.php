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
 
namespace Vegas\Db\Decorator\Helper;

/**
 * Class MappingHelperTrait
 * @package Vegas\Db\Decorator
 */
trait MappingHelperTrait
{
    /**
     * Defines field mappings in format
     *
     * <code>
     * array(
     *      'field_1' => 'mapper_1',
     *      'field_2' => array('mapper_1', 'mapper_2')
     * )
     * </code>
     * @var array
     */
    protected $mappings = array();

    /**
     * Returns array of attributes with mapped values
     *
     * @return array
     */
    public function toMappedArray()
    {
        $values = $this->toArray();
        $mappedValues = array();
        foreach ($values as $key => $value) {
            $mappedValues[$key] = $this->readMapped($key);
        }

        return $mappedValues;
    }

    /**
     * Returns mapped value of attribute
     *
     * @param $name
     * @return mixed
     */
    public function readMapped($name)
    {
        if (!$this->hasMapping($name) && isset($this->mappings[$name])) {
            $this->addMapping($name, $this->mappings[$name]);
        }
        $value = parent::readAttribute($name);

        $value = $this->resolveMapping($name, $value);

        return $value;
    }
} 