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
namespace Vegas\Db\Adapter\Mongo;

use Phalcon\DiInterface;
use Phalcon\Mvc\Collection\Manager;
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
     * @param DiInterface $di
     * @throws \Vegas\Db\Exception\NoRequiredServiceException
     */
    public function verifyRequiredServices(DiInterface $di)
    {
        if (!$di->has('mongo')) {
            throw new NoRequiredServiceException();
        }
    }

    /**
     * Setups extra services (if not exist) required by mongo service
     *
     * @param DiInterface $di
     */
    public function setupExtraServices(DiInterface $di)
    {
        if (!$di->has('collectionManager')) {
            $di->set('collectionManager', function() {
                return new Manager();
            });
        }
    }
}
