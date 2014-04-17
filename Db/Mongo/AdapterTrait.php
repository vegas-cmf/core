<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Db\Mongo;

use Vegas\Db\Exception\NoRequiredServiceException;

trait AdapterTrait
{
    public function verifyRequiredServices(\Phalcon\DiInterface $di)
    {
        if (!$di->has('mongo')) {
            throw new NoRequiredServiceException();
        }
    }

    public function setupExtraServices(\Phalcon\DiInterface $di)
    {
        if (!$di->has('collectionManager')) {
            $di->set('collectionManager', function() {
                return new \Phalcon\Mvc\Collection\Manager();
            });
        }
    }
}