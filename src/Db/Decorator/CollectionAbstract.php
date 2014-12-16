<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Db\Decorator;

use Phalcon\Mvc\Collection;
use Vegas\Db\Adapter\Mongo\RefResolverTrait;
use Vegas\Db\Decorator\Helper\MappingHelperTrait;
use Vegas\Db\Decorator\Helper\ReadNestedAttributeTrait;
use Vegas\Db\Decorator\Helper\SlugTrait;
use Vegas\Db\Decorator\Helper\WriteAttributesTrait;
use Vegas\Db\MappingResolverTrait;

/**
 * Class CollectionAbstract
 * @package Vegas\Db\Decorator
 */
abstract class CollectionAbstract extends Collection
{
    use MappingResolverTrait;
    use MappingHelperTrait;
    use SlugTrait;
    use WriteAttributesTrait;
    use ReadNestedAttributeTrait;
    use RefResolverTrait;

    /**
     * Event fired when record is being created
     */
    public function beforeCreate()
    {
        $this->created_at = new \MongoInt32(time());
    }

    /**
     * Event fired when record is being updated
     */
    public function beforeUpdate()
    {
        $this->updated_at = new \MongoInt32(time());
    }
}
