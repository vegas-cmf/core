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
namespace Vegas\Db;

/**
 * Interface AdapterInterface
 * @package Vegas\Db
 */
interface AdapterInterface
{
    /**
     * @param \Phalcon\DiInterface $di
     * @return mixed
     */
    public function verifyRequiredServices(\Phalcon\DiInterface $di);
}
