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

    public function onConstruct()
    {
        if(!$this->_id) {
            $this->_id = new \MongoId();
        }
    }

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

    /**
     * Returns an array with reserved properties that cannot be part of the insert/update
     */
    public function getReservedAttributes()
    {
        $reserved = self::$_reserved;
        if ($reserved === null) {
            $reserved = [
                '_connection' => true,
                '_dependencyInjector' => true,
                '_source' => true,
                '_operationMade' => true,
                '_errorMessages' => true,
                '_modelsManager' => true,
                '_skipped' => true,
                'cache' => true,
                'metadataCache' => true,
                'di' => true,
                '_collectionManager' => true,
                'mappingFieldsCache' => true,
                '__lazy_loading' => true,
                '__is_mapped' => true,
                '__is_property_mapped' => true,
                '__operation' => true,
                '__cursorFields' => true,
                '_dirtyState' => true,
                'mappings' => true
            ];
            self::$_reserved = $reserved;
        }
        return $reserved;
    }

    public function &toArray()
    {
        $data = parent::toArray();
        return $data;
    }
}
