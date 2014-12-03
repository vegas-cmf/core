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
namespace Vegas\DI;

/**
 * Interface ScaffoldingInterface
 * @package Vegas\DI
 */
interface ScaffoldingInterface
{
    /**
     * Constructor
     * Sets scaffolding adapter
     *
     * @param Scaffolding\AdapterInterface $adapter
     */
    public function __construct(\Vegas\DI\Scaffolding\AdapterInterface $adapter);

    /**
     * Returns scaffolding adapter
     *
     * @return Scaffolding\AdapterInterface
     */
    public function getAdapter();

    /**
     * Returns record object
     * If record is empty, new record will be created
     *
     * @return mixed
     */
    public function getRecord();

    /**
     * Creates and returns form instance
     *
     * @param null $entity
     * @return mixed
     */
    public function getForm($entity = null);

    /**
     * Sets form name
     *
     * @param $name
     * @return mixed
     */
    public function setFormName($name);

    /**
     * Sets model name
     *
     * @param $name
     * @return mixed
     */
    public function setModelName($name);

    /**
     * Retrieve record by its ID
     *
     * @param $id
     * @return mixed
     */
    public function doRead($id);

    /**
     * Retrieve list of records as paginator object
     *
     * @param int $page
     * @param int $limit
     * @return \Phalcon\Paginator\AdapterInterface
     */
    public function doPaginate($page = 1, $limit = 10);

    /**
     * Creates new record
     *
     * @param array $values
     * @return mixed
     */
    public function doCreate(array $values);

    /**
     * Updates existing record by its ID
     *
     * @param $id
     * @param array $values
     * @return mixed
     */
    public function doUpdate($id, array $values);

    /**
     * Deletes existing record by its ID
     *
     * @param $id
     * @return mixed
     */
    public function doDelete($id);
}
