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
     * Sets scaffolding instance
     *
     * @param \Vegas\DI\Scaffolding $scaffolding
     * @return mixed
     */
    public function setScaffolding(\Vegas\DI\Scaffolding $scaffolding);
}
