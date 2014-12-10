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

namespace Vegas\DI\Scaffolding\Adapter;

use Phalcon\DI;
use Vegas\Db\Adapter\Mongo\AdapterTrait;
use Vegas\Db\AdapterInterface;
use Vegas\DI\Scaffolding\AdapterInterface as ScaffoldingAdapterInterface;
use Vegas\DI\Scaffolding\Exception\RecordNotFoundException;
use Vegas\DI\Scaffolding;
use Vegas\Paginator\Adapter\Mongo as PaginatorAdapterMongo;

/**
 * Class Mongo
 *
 * Mongo adapter for scaffolding
 *
 * @package Vegas\DI\Scaffolding\Adapter
 */
class Mongo implements AdapterInterface, ScaffoldingAdapterInterface
{
    use AdapterTrait;

    /**
     * Scaffolding instance
     *
     * @var Scaffolding
     */
    protected $scaffolding;

    /**
     * Constructor
     * Verifies services required by Mongo
     */
    public function __construct()
    {
        $di = DI::getDefault();
        $this->verifyRequiredServices($di);
        $this->setupExtraServices($di);
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
    public function getPaginator($page = 1, $limit = 10)
    {
        return new PaginatorAdapterMongo(array(
            'model' => $this->scaffolding->getRecord(),
            'limit' => $limit,
            'page' => $page
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setScaffolding(Scaffolding $scaffolding) {
        $this->scaffolding = $scaffolding;

        return $this;
    }
}
