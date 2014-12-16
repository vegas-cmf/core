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
 * Class ReadNestedAttributeTrait
 * @package Vegas\Db\Decorator\Helper
 */
trait ReadNestedAttributeTrait
{
    /**
     * Reads nested object
     * Usage
     * <code>
     * //reading object
     * $coll = new Collection();
     * $coll->a = new \stdClass();
     * $coll->a->b = new \stdClass();
     * $coll->a->b->c = 'val';
     *
     * $coll->readNestedAttribute('a.b.c');//returns 'val'
     *
     * //reading array
     * $coll = new Collection();
     * $coll->a = [
     *      'b' => [
     *          'c' => 'val'
     *      ]
     * ];
     * $coll->readNestedAttribute('a.b.c');//returns 'val'
     * </code>
     *
     * @param $attributeName
     * @return mixed|null
     */
    public function readNestedAttribute($attributeName)
    {
        if (!$attributeName) {
            return null;
        }
        $keys = explode('.', $attributeName);
        return $this->traverseObject($this->readAttribute($keys[0]), array_splice($keys, 1));
    }

    /**
     * Recursive traversing object based on properties from array
     *
     * @param $obj
     * @param $keys
     * @return mixed
     */
    private function traverseObject($obj, $keys)
    {
        if (empty($keys)) {
            return $obj;
        }
        $key = current($keys);

        if (is_array($obj) && isset($obj[$key])) {
            return $this->traverseObject($obj[$key], array_slice($keys, 1));
        } else if (is_object($obj) && isset($obj->{$key})) {
            return $this->traverseObject($obj->{$key}, array_slice($keys, 1));
        } else {
            return null;
        }
    }

    /**
     * Returns attribute value by its name
     *
     * @param $attribute
     * @return mixed
     */
    abstract public function readAttribute($attribute);
} 