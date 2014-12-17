<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Tests\DI\Scaffolding\Adapter;

class MongoTest extends \Vegas\Test\TestCase
{

    public function testShouldSetupOwnCollectionManager()
    {
        $collectionManager = $this->getDI()->get('collectionManager');

        $this->getDI()->remove('collectionManager');
        new \Vegas\DI\Scaffolding\Adapter\Mongo();
        $this->assertInstanceOf('\Phalcon\Mvc\Collection\Manager', $this->getDI()->get('collectionManager'));


        $this->getDI()->set('collectionManager', $collectionManager, true);
    }

    public function testShouldThrowExceptionAboutMissingRequiredService()
    {
        $mongo = $this->getDI()->get('mongo');

        $this->getDI()->remove('mongo');

        $exception = null;
        try {
            new \Vegas\DI\Scaffolding\Adapter\Mongo();
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Db\Exception\NoRequiredServiceException', $exception);

        $this->getDI()->set('mongo', $mongo);
    }
}
 