<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Db\Decorator;

use Phalcon\Utils\Slug;
use Vegas\Db\HasMappingTrait;

abstract class CollectionAbstract extends \Phalcon\Mvc\Collection
{
    use HasMappingTrait;
    use MappingHelperTrait;

    protected $mappings = array();

    public function beforeCreate()
    {
        $this->created_at = new \MongoInt32(time());
    }

    public function beforeUpdate()
    {
        $this->updated_at = new \MongoInt32(time());
    }

    public function generateSlug($string)
    {
        $slug = new Slug();
        $this->slug = $slug->generate($string);
    }
    
    public function writeAttributes($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->writeAttribute($attribute, $value);
        }
    }
}
