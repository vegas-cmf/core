<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Db\Decorator;

use Vegas\Db\Decorator\Helper\MappingHelperTrait;
use Vegas\Db\Decorator\Helper\SlugTrait;
use Vegas\Db\Decorator\Helper\WriteAttributesTrait;
use Vegas\Db\MappingResolverTrait;

/**
 * Class CollectionAbstract
 * @package Vegas\Db\Decorator
 */
abstract class CollectionAbstract extends \Phalcon\Mvc\Collection
{
    use MappingResolverTrait;
    use MappingHelperTrait;
    use SlugTrait;
    use WriteAttributesTrait;

    public function beforeCreate()
    {
        $this->created_at = new \MongoInt32(time());
    }

    public function beforeUpdate()
    {
        $this->updated_at = new \MongoInt32(time());
    }
}
