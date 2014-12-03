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

namespace Vegas\Db\Mapping;

use Vegas\Db\MappingInterface;

/**
 * Class DateTime
 *
 * Simple mapper for decoding JSON value
 *
 * @package Vegas\Db\Mapping
 */
class DateTime implements MappingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dateTime';
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(& $value)
    {
        if (is_integer($value) && strlen($value) > 0) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($value);
            $value = $dateTime->format('Y-m-d H:i:s');
        }

        return $value;
    }
}