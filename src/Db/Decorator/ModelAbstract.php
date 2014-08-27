<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
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
 * Class ModelAbstract
 * @package Vegas\Db\Decorator
 */
abstract class ModelAbstract extends \Phalcon\Mvc\Model
{
    use MappingResolverTrait;
    use MappingHelperTrait;
    use SlugTrait;
    use WriteAttributesTrait;

    /**
     * Event fired when record is being created
     */
    public function beforeCreate()
    {
        $this->created_at = time();
    }

    /**
     * Event fired when record is being updated
     */
    public function beforeUpdate()
    {
        $this->updated_at = time();
    }
    
    /**
     * Finds record by its ID
     *
     * @param $id
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function findById($id)
    {
        return parent::findFirst(array(
            "conditions" => "id = ?1",
            "bind" => array(1 => $id)
        ));
    }

    /**
     * Returns ID
     *
     * @return \Phalcon\Mvc\Model\Resultset
     */
    public function getId()
    {
        return $this->id;
    }
}