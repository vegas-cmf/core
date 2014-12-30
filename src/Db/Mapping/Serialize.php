<?php
/**
 * This file is part of Vegas package
 *
 * @author Radosław Fąfara <radek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Db\Mapping;

use Vegas\Db\MappingInterface;

/**
 * Class Serialize
 *
 * Simple mapper for PHP serialized fields
 *
 * @package Vegas\Db\Mapping
 */
class Serialize implements MappingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'serialize';
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(& $value)
    {
        if (is_string($value) && strlen($value) > 0) {
            $unserialized = unserialize($value);
            if ($value !== serialize(false) && $unserialized !== false) {
                $value = $unserialized;
            }
        }

        return $value;
    }
}
