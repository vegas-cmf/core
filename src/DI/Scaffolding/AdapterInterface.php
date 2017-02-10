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
namespace Vegas\DI\Scaffolding;

use Vegas\DI\Scaffolding;

/**
 * Interface AdapterInterface
 * @package Vegas\DI\Scaffolding
 */
interface AdapterInterface
{
    /**
     * Retrieves record by its ID
     *
     * @param $id
     * @return mixed
     */
    public function retrieveOne($id);

    /**
     * Sets query for the paginator
     *
     * @param $id
     * @return mixed
     */
    public function setQuery($query);

    /**
     * Retrieve list of records as paginator object.
     *
     * @param int $page
     * @param int $limit
     * @return \Phalcon\Paginator\AdapterInterface
     */
    public function getPaginator($page = 1, $limit = 10);

    /**
     * Sets scaffolding instance
     *
     * @param Scaffolding $scaffolding
     * @return mixed
     */
    public function setScaffolding(Scaffolding $scaffolding);
}
