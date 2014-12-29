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
 * Class Decimal
 *
 * Simple mapper for number fields
 *
 * @package Vegas\Db\Mapping
 */
class Decimal implements MappingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'decimal';
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(& $value)
    {
        if (is_numeric($value) && strlen($value) > 0) {
            $value = number_format($value, 2, '.', '');
        }

        return $value;
    }
}