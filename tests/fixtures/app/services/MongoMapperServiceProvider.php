<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Vegas\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class MongoMapperServiceProvider implements ServiceProviderInterface
{
    const SERVICE_NAME = 'mongoMapper';

    /**
     * {@inheritdoc}
     */
    public function register(DiInterface $di)
    {
        $di->set(self::SERVICE_NAME, function() use ($di) {
            $mapper = new \Vegas\Db\Adapter\Mongo\Mapper(APP_ROOT . '/app/modules/');
            $mapper->create(APP_ROOT . '/app/config/mongo.map.php');
            return $mapper;
        }, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            CollectionManagerServiceProvider::SERVICE_NAME,
            MongoMapperServiceProvider::SERVICE_NAME
        ];
    }
} 