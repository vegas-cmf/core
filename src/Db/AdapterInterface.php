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
namespace Vegas\Db;
use Phalcon\DiInterface;

/**
 * Interface AdapterInterface
 * @package Vegas\Db
 */
interface AdapterInterface
{
    /**
     * Verifies services required by db adapter
     *
     * @param DiInterface $di
     * @return mixed
     */
    public function verifyRequiredServices(DiInterface $di);
}
