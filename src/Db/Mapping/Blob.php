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
 * Class Blob
 *
 * Simple mapper for binary fields
 *
 * @package Vegas\Db\Mapping
 */
class Blob implements MappingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'blob';
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(& $value)
    {
        if (is_string($value) && strlen($value) > 0) {
            $decoded = base64_decode($value, true);
            $decoded !== false && $value = $decoded;
        }

        return $value;
    }
}