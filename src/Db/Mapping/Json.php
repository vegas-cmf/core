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
 
namespace Vegas\Db\Mapping;

use Vegas\Db\MappingInterface;

/**
 * Class Json
 *
 * Simple mapper for decoding JSON value
 *
 * @package Vegas\Db\Mapping
 */
class Json implements MappingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(& $value)
    {
        if (is_string($value) && strlen($value) > 0) {
            $value = json_decode($value, true);
        }

        return $value;
    }
}