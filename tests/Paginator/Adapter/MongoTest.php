<?php
/**
 * This file is part of Vegas package
 *
 * @author Mateusz Aniolek <mateusz.aniolek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Paginator;

use Phalcon\DI;
use Vegas\Tests\Stub\Models\FakePaginatorModel;

class MongoTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $model = new FakePaginatorModel;
        $model->fake_field = 1;
        $model->save();

        $model = new FakePaginatorModel;
        $model->fake_field = 2;
        $model->save();

        $model = new FakePaginatorModel;
        $model->fake_field = 3;
        $model->save();

    }

    public function testPaginator()
    {
        $adapter = new \Vegas\Paginator\Adapter\Mongo(array(
            'modelName' => '\Vegas\Tests\Stub\Models\FakePaginatorModel',
            'limit' => 10,
            'page' => 1
        ));
        $results = $adapter->getResults();

        $this->assertCount(3, $results);
    }

    public function tearDown()
    {
        parent::tearDown();

        foreach (FakePaginatorModel::find() as $robot) {
            $robot->delete();
        }
    }

} 