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
 * Simple mapper for DateTime object
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
        if (is_numeric($value) && strlen($value) > 0) {
            $dateTime = new \Vegas\Util\DateTime();
            $dateTime->setTimestamp($value);
            $value = $dateTime;
        } else if (\Vegas\Util\DateTime::isValid($value)) {
            $dateTime = new \Vegas\Util\DateTime($value);
            $value = $dateTime;
        }

        return $value;
    }
}
