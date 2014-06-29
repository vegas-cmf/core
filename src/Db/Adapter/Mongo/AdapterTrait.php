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
namespace Vegas\Db\Adapter\Mongo;

use Vegas\Db\Exception\NoRequiredServiceException;

/**
 * Trait AdapterTrait
 *
 * Should be use for classes that use Mongo database adapter
 *
 * @package Vegas\Db\Adapter\Mongo
 */
trait AdapterTrait
{
    /**
     * Verifies required services for Mongo adapter
     *
     * @param \Phalcon\DiInterface $di
     * @throws \Vegas\Db\Exception\NoRequiredServiceException
     */
    public function verifyRequiredServices(\Phalcon\DiInterface $di)
    {
        if (!$di->has('mongo')) {
            throw new NoRequiredServiceException();
        }
    }

    /**
     * Setups extra services (if not exist) required by mongo service
     *
     * @param \Phalcon\DiInterface $di
     */
    public function setupExtraServices(\Phalcon\DiInterface $di)
    {
        if (!$di->has('collectionManager')) {
            $di->set('collectionManager', function() {
                return new \Phalcon\Mvc\Collection\Manager();
            });
        }
    }
}