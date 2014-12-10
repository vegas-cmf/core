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
use Phalcon\DiInterface;
use Phalcon\Paginator\Adapter\Model as PaginatorAdapterModel;
use Vegas\Db\AdapterInterface;
use Vegas\Db\Exception\NoRequiredServiceException;
use Vegas\DI\Scaffolding\AdapterInterface as ScaffoldingAdapterInterface;
use Vegas\DI\Scaffolding\Exception\RecordNotFoundException;
use Vegas\DI\Scaffolding;

/**
 * Class Mysql
 *
 * Mysql adapter for scaffolding
 *
 * @package Vegas\DI\Scaffolding\Adapter
 */
class Mysql implements AdapterInterface, ScaffoldingAdapterInterface
{
    /**
     * Scaffolding instance
     *
     * @var Scaffolding
     */
    protected $scaffolding;

    /**
     * Constructor
     */
    public function __construct()
    {
        $di = DI::getDefault();
        $this->verifyRequiredServices($di);
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
        return new PaginatorAdapterModel(array(
            'data' => call_user_func(array($this->scaffolding->getRecord(),'find')),
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

    /**
     * {@inheritdoc}
     */
    public function verifyRequiredServices(DiInterface $di)
    {
        if (!$di->has('db')) {
            throw new NoRequiredServiceException();
        }
    }
}
