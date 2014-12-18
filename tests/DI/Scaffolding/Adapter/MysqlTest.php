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

namespace Vegas\Tests\DI\Scaffolding\Adapter;

use Phalcon\DI;
use Vegas\DI\Scaffolding;

class MysqlTest extends \Vegas\Test\TestCase
{
    public static function setUpBeforeClass()
    {
        $di = DI::getDefault();
        $di->get('db')->execute('DROP TABLE IF EXISTS fake ');
        $di->get('db')->execute(
            'CREATE TABLE fake(
            id int not null primary key auto_increment,
            fake_field varchar(250) null,
            created_at int null
            )'
        );
    }

    public static function tearDownAfterClass()
    {
        $di = DI::getDefault();
        $di->get('db')->execute('DROP TABLE IF EXISTS fake ');
    }

    public function tearDown()
    {
        foreach (\Test\Models\Fake::find() as $r) {
            $r->delete();
        }
    }

    public function testShouldThrowExceptionAboutMissingRequiredService()
    {
        $db = $this->getDI()->get('db');

        $this->getDI()->remove('db');

        $exception = null;
        try {
            new \Vegas\DI\Scaffolding\Adapter\Mysql();
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Db\Exception\NoRequiredServiceException', $exception);

        $this->getDI()->set('db', $db);
    }

    public function testShouldThrowExceptionAboutMissingScaffolding()
    {
        $exception = null;
        try {
            $mysql = new \Vegas\DI\Scaffolding\Adapter\Mysql();
            $mysql->retrieveOne(1);
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\DI\Scaffolding\Exception\MissingScaffoldingException', $exception);
    }

    public function testShouldRetrieveRecordByItsId()
    {
        $mysql = new \Vegas\DI\Scaffolding\Adapter\Mysql();
        $scaffolding = new Scaffolding($mysql);

        $scaffolding->setModelName('\Test\Models\Fake');
        $scaffolding->setFormName('\Test\Forms\Fake');
        $created = $scaffolding->doCreate([
            'fake_field' => 'fake'
        ]);
        $this->assertTrue($created);

        $this->assertInstanceOf('\Test\Models\Fake', $mysql->retrieveOne($scaffolding->getRecord()->getId()));
    }

    public function testShouldReturnValidPagination()
    {
        $mysql = new \Vegas\DI\Scaffolding\Adapter\Mysql();
        $scaffolding = new Scaffolding($mysql);

        $scaffolding->setModelName('\Test\Models\Fake');
        $scaffolding->setFormName('\Test\Forms\Fake');
        $scaffolding->doCreate([
            'fake_field' => 'fake'
        ]);
        $scaffolding->doCreate([
            'fake_field' => 'fake2'
        ]);

        $pagination = $mysql->getPaginator();
        $this->assertInstanceOf('\Phalcon\Paginator\Adapter\Model', $pagination);
        $this->assertInstanceOf('\stdClass', $pagination->getPaginate());
    }
}