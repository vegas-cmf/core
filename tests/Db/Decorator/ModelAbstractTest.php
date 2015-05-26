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

namespace Vegas\Tests\Db\Decorator;

use Phalcon\DI;
use Phalcon\Utils\Slug;
use Vegas\Db\Decorator\Helper\RepositoryTrait;
use Vegas\Db\Decorator\ModelAbstract;

class FakeModel extends ModelAbstract
{
    public $id;

    public $title;

    public $content;

    public $category_id;

    public $created_at;

    public $updated_at;

    public function getSource()
    {
        return 'fake_table';
    }
}

//@TODO reuse when Phalcon team will unify Model- and CollectionInterfaces
//@TODO check https://github.com/phalcon/cphalcon/issues/10406
//abstract class FakeRepository extends FakeModel
//{
//
//    use RepositoryTrait;
//}

class ModelAbstractTest extends \PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        $di = DI::getDefault();
        $di->get('db')->execute('DROP TABLE IF EXISTS fake_table ');
        $di->get('db')->execute(
            'CREATE TABLE fake_table(
            id int not null primary key auto_increment,
            title varchar(250) null,
            content text null,
            category_id int null,
            created_at int null
            )'
        );
    }

    public static function tearDownAfterClass()
    {
        $di = DI::getDefault();
        $di->get('db')->execute('DROP TABLE IF EXISTS fake_table ');
    }

    public function testShouldCreateRecord()
    {
        $data = [
            'title' =>  'Title test',
            'content'   =>  'Content test',
            'category_id' => rand(1000, 9999)
        ];

        $fake = new FakeModel();
        $this->assertInstanceOf('\Vegas\Db\Decorator\ModelAbstract', $fake);
        $fake->writeAttributes($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $fake->readAttribute($key));
        }

        $fake->generateSlug($data['title']);

        $slug = new Slug();
        $this->assertEquals($slug->generate($data['title']), $fake->readAttribute('slug'));
        $this->assertEquals($data['title'], $fake->readMapped('title'));
        $this->assertEquals(json_encode($fake->toArray()), json_encode($fake->toMappedArray()));

        $this->assertTrue($fake->save());
    }

    /**
     * @depends testShouldCreateRecord
     */
    public function testShouldFindRecordByItsId()
    {
        $fake = FakeModel::findFirst();

        $this->assertSame(
            $fake->toArray(),
            FakeModel::findById($fake->getId())->toArray()
        );
    }

    /**
     * @depends testShouldCreateRecord
     */
    public function testShouldUpdateRecord()
    {
        $fake = FakeModel::findFirst();
        $fake->title = 'New title';

        $this->assertTrue($fake->save());
        $this->assertInternalType('int', $fake->updated_at);

        $fake = FakeModel::findFirst();
        $this->assertEquals('New title', $fake->title);
    }
}