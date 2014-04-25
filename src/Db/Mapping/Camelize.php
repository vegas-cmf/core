<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Db\Mapping;

use Vegas\Db\MappingInterface;

/**
 * Class Camelize
 *
 * Simple mapper for converting text to camelize style
 *
 * @package Vegas\Db\Mapping
 */
class Camelize implements MappingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'camelize';
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(& $value)
    {
        if (is_string($value) && strlen($value) > 0) {
            $value = \Phalcon\Text::camelize($value);
        }

        return $value;
    }
}