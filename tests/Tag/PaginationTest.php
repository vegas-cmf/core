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
namespace Vegas\Tests\Tag;

use Vegas\Paginator\Adapter\Mongo;
use Vegas\Paginator\Page;
use Vegas\Tag\Pagination;
use Vegas\Tests\App\TestCase;
use Vegas\Tests\Stub\Models\FakeModel;

class RequestMock
{
    public function get()
    {
        return [
            'by' => 'name',
            'order' => 'asc'
        ];
    }
}

class PaginationTest extends TestCase
{
    public function testException()
    {
        try {
            $paginator = new Mongo(array());
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Paginator\Adapter\Exception\ModelNotSetException', $ex);
        }

        try {
            $paginator = new Mongo(array(
                'modelName' => '\Vegas\Tests\Stub\Models\FakeModel'
            ));
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Paginator\Adapter\Exception\DbNotSetException', $ex);
        }
    }

    public function testRender()
    {
        $this->createObjectCollection();

        $pagination = new Pagination($this->di);

        $paginator = new Mongo(array(
            'db' => $this->di->get('mongo'),
            'modelName' => '\Vegas\Tests\Stub\Models\FakeModel',
            'query' => array('test_name' => 'PaginationTest'),
            'limit' => 10,
            'page' => 1,
            'sort' => [
                'created_at' => -1
            ]
        ));

        $this->assertEquals(
            '<ul class="pagination"><li class="prev not-active"><a href="/?page=1">Previous</a></li><li class="active"><a href="/?page=1">1</a></li><li class=""><a href="/?page=2">2</a></li><li class="next"><a href="/?page=2">Next</a></li></ul>',
            $pagination->render($paginator->getPaginate())
        );

        $paginator = new Mongo(array(
            'db' => $this->di->get('mongo'),
            'modelName' => '\Vegas\Tests\Stub\Models\FakeModel',
            'query' => array('test_name' => 'PaginationTest'),
            'page' => 2
        ));

        $this->assertEquals(
            '<ul class="pagination"><li class="prev"><a href="/?page=1">Previous</a></li><li class=""><a href="/?page=1">1</a></li><li class="active"><a href="/?page=2">2</a></li><li class="next not-active"><a href="/?page=2">Next</a></li></ul>',
            $pagination->render($paginator->getPaginate())
        );

        $paginate = $paginator->getPaginate();
        $paginate->current = '99';
        $paginate->total_pages = '2';

        $this->assertEquals(
            '<ul class="pagination"><li class="prev"><a href="/?page=1">Previous</a></li><li class=""><a href="/?page=1">1</a></li><li class=""><a href="/?page=2">2</a></li><li class="next not-active"><a href="/?page=2">Next</a></li></ul>',
            $pagination->render($paginate)
        );


        $paginate = $paginator->getPaginate();
        $paginate->currentUri = 'test';
        $this->assertEquals(
            '<ul class="pagination"><li class="prev"><a href="test?page=1">Previous</a></li><li class=""><a href="test?page=1">1</a></li><li class="active"><a href="test?page=2">2</a></li><li class="next not-active"><a href="test?page=2">Next</a></li></ul>',
            $pagination->render($paginate)
        );

        $page = $paginator->getPaginate();
        $this->assertSame(1, $page->before);
        $this->assertSame(null, $page->next);
        $this->assertSame(2, $page->current);
        $this->assertSame(10, count($page->items));
        $this->assertSame(2, $page->total_pages);
    }

    public function testArguments()
    {
        $this->createObjectCollection();
        $this->di->setShared('request', function(){
           return new RequestMock();
        });

        $pagination = new Pagination($this->di);

        $paginator = new Mongo(array(
            'db' => $this->di->get('mongo'),
            'modelName' => '\Vegas\Tests\Stub\Models\FakeModel',
            'query' => array('test_name' => 'PaginationTest'),
            'limit' => 10,
            'page' => 1,
            'sort' => [
                'created_at' => -1
            ]
        ));

        $this->assertEquals(
            '<ul class="pagination"><li class="prev not-active"><a href="/?page=1&by=name&order=asc">Previous</a></li><li class="active"><a href="/?page=1&by=name&order=asc">1</a></li><li class=""><a href="/?page=2&by=name&order=asc">2</a></li><li class="next"><a href="/?page=2&by=name&order=asc">Next</a></li></ul>',
            $pagination->render($paginator->getPaginate(), ['sorting' => ['by' => 'name', 'order' => 'asc']])
        );

    }

    private function createObjectCollection()
    {
        for ($i = 0; $i<20; $i++) {
            $fakeModel = new FakeModel();
            $fakeModel->fake_field = uniqid();
            $fakeModel->test_name = 'PaginationTest';
            $fakeModel->save();
        }
    }

    public function tearDown()
    {
        $fakeModels = FakeModel::find(array(array(
            'test_name' => 'PaginationTest'
        )));

        foreach ($fakeModels As $fakeModel) {
            $fakeModel->delete();
        }
    }
}