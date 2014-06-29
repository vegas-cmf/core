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

use Phalcon\DiInterface;
use Vegas\DI\ServiceProviderInterface;
use Vegas\DI\Scaffolding;

/**
 * Class ScaffoldingServiceProvider
 */
class ScaffoldingServiceProvider implements ServiceProviderInterface
{
    const SERVICE_NAME = 'scaffolding';

    /**
     * {@inheritdoc}
     */
    public function register(DiInterface $di)
    {
        $adapter = new Scaffolding\Adapter\Mongo();
        $di->set(self::SERVICE_NAME, new Scaffolding($adapter), true);
    }

    public function getDependencies()
    {
        return array(
            MongoServiceProvider::SERVICE_NAME,
            CollectionManagerServiceProvider::SERVICE_NAME
        );
    }
} 