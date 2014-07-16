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

namespace Vegas\DI\Scaffolding\Adapter;

use Phalcon\DI;
use Vegas\DI\Scaffolding\Exception\RecordNotFoundException;

/**
 * Class Mysql
 *
 * Mysql adapter for scaffolding
 *
 * @package Vegas\DI\Scaffolding\Adapter
 */
class Mysql implements \Vegas\Db\AdapterInterface, \Vegas\DI\Scaffolding\AdapterInterface
{
    use \Vegas\Db\Adapter\Mongo\AdapterTrait;

    /**
     * Scaffolding instance
     *
     * @var \Vegas\DI\Scaffolding
     */
    protected $scaffolding;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveOne($id)
    {
        $record = call_user_func(array($this->scaffolding->getRecord(),'findById'),$id);
        
        if (!$record) {
            throw new RecordNotFoundException();
        }
        
        return $record;
    }

    /**
     * {@inheritdoc}
     */
    public function setScaffolding(\Vegas\DI\Scaffolding $scaffolding) {
        $this->scaffolding = $scaffolding;
        
        return $this;
    }

}
