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

class AggregateMongoTest extends \PHPUnit_Framework_TestCase
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

    public function testEmptyQuery()
    {
        $adapter = new \Vegas\Paginator\Adapter\AggregateMongo(array(
            'db' => \Phalcon\DI::getDefault()->get('mongo'),
            'modelName' => '\Vegas\Tests\Stub\Models\FakePaginatorModel'
        ));
        $results = $adapter->getResults();

        $this->assertCount(0, $results);
    }

    public function testPaginator()
    {
        $adapter = new \Vegas\Paginator\Adapter\AggregateMongo(array(
            'db' => \Phalcon\DI::getDefault()->get('mongo'),
            'modelName' => '\Vegas\Tests\Stub\Models\FakePaginatorModel',
            'query' => [
                [
                    '$project' => [
                        'fake_field' => 1,
                        'current' => '$$CURRENT',
                        'insensitive' => ['$toLower' => '$fake_field']
                    ]
                ],
                [
                    '$sort' => ['insensitive' => 1]
                ]
            ]
        ));
        $results = $adapter->getResults();

        $this->assertCount(3, $results);
    }

    public function testCursor()
    {
        $adapter = new \Vegas\Paginator\Adapter\AggregateMongo(array(
            'db' => \Phalcon\DI::getDefault()->get('mongo'),
            'modelName' => '\Vegas\Tests\Stub\Models\FakePaginatorModel',
            'limit' => 10,
            'page' => 1,
            'aggregate' => [
                [
                    '$project' => [
                        'fake_field' => 1,
                        'current' => '$$CURRENT',
                        'insensitive' => ['$toLower' => '$fake_field']
                    ]
                ],
                [
                    '$sort' => ['insensitive' => 1]
                ]
            ]
        ));

        $cursor = $adapter->getCursor();
        $value = $cursor->current();
        $this->assertEquals("1", $value['fake_field']);

        $cursor->skip();
        $value = $cursor->current();
        $this->assertEquals("2", $value['fake_field']);

        $this->assertEquals(3, $cursor->count());

        $this->assertTrue($cursor->valid());

        $cursor->skip(2);
        $this->assertFalse($cursor->valid());

    }

    public function tearDown()
    {
        parent::tearDown();

        foreach (FakePaginatorModel::find() as $robot) {
            $robot->delete();
        }
    }

} 