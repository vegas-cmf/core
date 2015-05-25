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

namespace Vegas\Db\Decorator\Helper;

/**
 * Trait RepositoryTrait
 * Sample trait to be used by Model/Collection repositories.
 * @package Vegas\Db\Decorator\Helper
 */
trait RepositoryTrait
{
    /**
     * {@inheritdoc}
     */
    public static function find(array $parameters = null)
    {
        $classname = get_parent_class(__CLASS__);
        return $classname::find($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public static function findFirst(array $parameters = null)
    {
        $classname = get_parent_class(__CLASS__);
        return $classname::findFirst($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public static function findById($id)
    {
        $classname = get_parent_class(__CLASS__);
        return $classname::findById($id);
    }
}
