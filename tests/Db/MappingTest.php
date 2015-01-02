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
 
namespace Vegas\Tests\Db;

use Phalcon\DI;
use Vegas\Db\Decorator\CollectionAbstract;
use Vegas\Db\Decorator\ModelAbstract;
use Vegas\Db\Exception\InvalidMappingClassException;
use Vegas\Db\Exception\MappingClassNotFoundException;
use Vegas\Db\Mapping\Blob;
use Vegas\Db\Mapping\Camelize;
use Vegas\Db\Mapping\DateTime as DateTimeMapping;
use Vegas\Db\Mapping\Decimal;
use Vegas\Db\Mapping\Json;
use Vegas\Db\Mapping\Lowercase;
use Vegas\Db\Mapping\Serialize;
use Vegas\Db\Mapping\Uppercase;
use Vegas\Db\MappingManager;
use Vegas\Util\DateTime;

class Fake extends CollectionAbstract
{
    public function getSource()
    {
        return 'fake';
    }

    protected $mappings = [
        'somedata'  =>  'json',
        'somecamel' =>  'camelize',
        'encoded'   =>  'blob',
        'upperstring' => 'uppercase',
        'phpobject' => 'serialize',
        'currency' => 'decimal'
    ];
}

class FakeModel extends ModelAbstract
{
    public function getSource()
    {
        return 'fake_table';
    }

    protected $mappings = [
        'somedata'  =>  'json',
        'somecamel' =>  'camelize',
        'encoded'   =>  'blob'
    ];
}

class FakeDate extends CollectionAbstract
{
    public function getSource()
    {
        return 'fake_date';
    }

    protected $mappings = [
        'createdAt' => 'dateTime'
    ];
}

class FakeMultiple extends CollectionAbstract
{
    public function getSource()
    {
        return 'fake_multiple';
    }

    protected $mappings = [
        'jsonlower' => ['json', 'lowercase']
    ];
}

class FakeClass
{
    public $param;

    public function __construct($param)
    {
        $this->param = $param;
    }
}

class MappingTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $di = \Phalcon\DI::getDefault();
        $di->get('db')->execute('DROP TABLE IF EXISTS fake_table ');
        $di->get('db')->execute(
            'CREATE TABLE fake_table(
            id int not null primary key auto_increment,
            somedata varchar(250) null,
            somecamel varchar(250) null
            )'
        );
    }

    public static function tearDownAfterClass()
    {
        $di = \Phalcon\DI::getDefault();

        foreach (Fake::find() as $fake) {
            $fake->delete();
        }

        $di->get('db')->execute('DROP TABLE IF EXISTS fake_table ');
    }

    public function testShouldAddMapperToMappingManager()
    {
        //define mappings
        $mappingManager = new MappingManager;

        $this->assertInternalType('array', $mappingManager->getMappers());
        $this->assertEmpty($mappingManager->getMappers());

        $mappingManager->add(new Json);
        $mappingManager->add(new Camelize);
        $mappingManager->add(new Uppercase);

        $this->assertInternalType('array', $mappingManager->getMappers());
        $this->assertNotEmpty($mappingManager->getMappers());

        $this->assertNotEmpty(MappingManager::find('json'));
        $this->assertInstanceOf('\Vegas\Db\MappingInterface', MappingManager::find('json'));

        $this->assertNotEmpty(MappingManager::find('camelize'));
        $this->assertInstanceOf('\Vegas\Db\MappingInterface', MappingManager::find('camelize'));

        try {
            $mappingManager->add(new \stdClass);
        } catch (InvalidMappingClassException $e) {
            $m = $e->getMessage();
        }
        $this->assertEquals('Mapping class must be an instance of MappingInterface', $m);

        try {
            $mappingManager->remove('json');
            $mappingManager->find('json');
        } catch (MappingClassNotFoundException $e) {
            $m = $e->getMessage();
        }
        $this->assertEquals(sprintf('Mapping class \'%s\' was not found', 'json'), $m);

        try {
            $mappingManager->find('fake_mapping');
        } catch (MappingClassNotFoundException $e) {
            $m = $e->getMessage();
        }
        $this->assertEquals(sprintf('Mapping class \'%s\' was not found', 'fake_mapping'), $m);
    }

    public function testShouldResolveCollectionMappings()
    {
        $mappingManager = new MappingManager;
        $mappingManager->add(new Json);
        $mappingManager->add(new Camelize);
        $mappingManager->add(new Blob);
        $mappingManager->add(new Uppercase);

        DI::getDefault()->get('mongo')->selectCollection('fake')->remove([]);

        /**  CREATE RECORD  */
        $fake = new Fake();

        $someData = json_encode([1,2,3,4,5,6]);
        $fake->somedata = $someData;

        $nonCamelText = 'this_is_non_camel_case_text';
        $fake->somecamel = $nonCamelText;

        $encodedVal = base64_encode('test');
        $fake->encoded = $encodedVal;

        $lowerTxt = 'some lower case text';
        $fake->upperstring = $lowerTxt;

        $this->assertTrue($fake->save());
        /**  CREATE RECORD  */

        /** TEST VALUES */
        $fakeDoc = Fake::findFirst();

        $this->assertInternalType('array', $fakeDoc->readMapped('somedata'));
        $this->assertEquals(\Phalcon\Text::camelize($nonCamelText), $fakeDoc->readMapped('somecamel'));
        $this->assertEquals('test', $fakeDoc->readMapped('encoded'));
        $this->assertEquals(strtoupper($lowerTxt), $fakeDoc->readMapped('upperstring'));

        $this->assertEquals($nonCamelText, $fakeDoc->somecamel);
        $this->assertEquals($someData, $fakeDoc->somedata);
        $this->assertEquals($encodedVal, $fakeDoc->encoded);
        $this->assertEquals($lowerTxt, $fakeDoc->upperstring);
        $this->assertEquals($someData, $fakeDoc->readAttribute('somedata'));
        $this->assertEquals($nonCamelText, $fakeDoc->readAttribute('somecamel'));
        $this->assertEquals($encodedVal, $fakeDoc->readAttribute('encoded'));
        $this->assertEquals($encodedVal, $fakeDoc->readAttribute('encoded'));
        $this->assertEquals($lowerTxt, $fakeDoc->readAttribute('upperstring'));

        $ownMappedValues = [
            '_id'   =>  $fakeDoc->readMapped('_id'),
            'somedata'   =>  $fakeDoc->readMapped('somedata'),
            'somecamel'   =>  $fakeDoc->readMapped('somecamel'),
            'encoded'   =>  $fakeDoc->readMapped('encoded'),
            'upperstring'   =>  $fakeDoc->readMapped('upperstring')
        ];
        $mappedValues = $fakeDoc->toMappedArray();

        $this->assertEquals($mappedValues['somedata'], $ownMappedValues['somedata']);
        $this->assertEquals($mappedValues['somecamel'], $ownMappedValues['somecamel']);
        $this->assertEquals($mappedValues['encoded'], $ownMappedValues['encoded']);
        $this->assertEquals($mappedValues['upperstring'], $ownMappedValues['upperstring']);


        $fakeDoc->removeMapping('somedata');
        $this->assertEquals($someData, $fakeDoc->readMapped('somedata'));

        $fakeDoc->clearMappings();
        $this->assertEquals($nonCamelText, $fakeDoc->readMapped('somecamel'));
    }

    public function testShouldResolveModelMappings()
    {
        $mappingManager = new MappingManager();
        $mappingManager->add(new Json());
        $mappingManager->add(new Camelize());

        $someData = json_encode([1,2,3,4,5,6]);
        $fake = new FakeModel;
        $fake->somedata = $someData;
        $nonCamelText = 'this_is_non_camel_case_text';
        $fake->somecamel = $nonCamelText;
        $this->assertTrue($fake->save());

        $fakeRecord = FakeModel::findFirst();

        $this->assertInternalType('array', $fakeRecord->readMapped('somedata'));
        $this->assertEquals(\Phalcon\Text::camelize($nonCamelText), $fakeRecord->readMapped('somecamel'));

        $this->assertEquals($nonCamelText, $fakeRecord->somecamel);
        $this->assertEquals($someData, $fakeRecord->somedata);
        $this->assertEquals($someData, $fakeRecord->readAttribute('somedata'));
        $this->assertEquals($nonCamelText, $fakeRecord->readAttribute('somecamel'));

        $ownMappedValues = [
            '_id'   =>  $fakeRecord->readMapped('_id'),
            'somedata'   =>  $fakeRecord->readMapped('somedata'),
            'somecamel'   =>  $fakeRecord->readMapped('somecamel'),
        ];
        $mappedValues = $fakeRecord->toMappedArray();

        $this->assertEquals($mappedValues['somedata'], $ownMappedValues['somedata']);
        $this->assertEquals($mappedValues['somecamel'], $ownMappedValues['somecamel']);

        $fakeRecord->removeMapping('somedata');
        $this->assertEquals($someData, $fakeRecord->readMapped('somedata'));

        $fakeRecord->clearMappings();
        $this->assertEquals($nonCamelText, $fakeRecord->readMapped('somecamel'));
    }

    public function testShouldResolveDateTime()
    {
        $mappingManager = new MappingManager;
        $mappingManager->add(new DateTimeMapping);

        $now = new \DateTime('now');

        $fake = new FakeDate();
        $fake->createdAt = time();

        $this->assertEquals($now->format(DateTime::$globalDefaultFormat), $fake->readMapped('createdAt'));
        $this->assertInstanceOf('\Vegas\Util\DateTime', $fake->readMapped('createdAt'));
        $this->assertSame($fake->createdAt, (int)$fake->readMapped('createdAt')->format('U'));

        $fake->createdAt = $now->format('m/d/Y');
        $this->assertInstanceOf('\Vegas\Util\DateTime', $fake->readMapped('createdAt'));
        $this->assertEquals($now->format('m/d/Y'), $fake->readMapped('createdAt')->format('m/d/Y'));
        $this->assertSame($fake->createdAt, $fake->readMapped('createdAt')->format('m/d/Y'));

        $fake->createdAt = $now->format('d/m/Y');
        if ($now->format('j') > 12) {
            $this->assertNotInstanceOf('\Vegas\Util\DateTime', $fake->readMapped('createdAt'));
            $this->assertEquals($now->format('d/m/Y'), $fake->readMapped('createdAt'));
            $this->assertInternalType('string', $fake->readMapped('createdAt'));
        } else {
            $this->assertInstanceOf('\Vegas\Util\DateTime', $fake->readMapped('createdAt'));
            $this->assertNotEquals($now->format('d/m/Y'), $fake->readMapped('createdAt')->format('d/m/Y'));
        }
    }

    public function testShouldResolveMultipleMappersForOneField()
    {
        $mappingManager = new MappingManager;
        $mappingManager->add('\Vegas\Db\Mapping\Json');
        $mappingManager->add(new Lowercase);

        $fake = new FakeMultiple;
        $fake->jsonlower = json_encode('SOME DATA');

        $this->assertSame('some data', $fake->readMapped('jsonlower'));
        $this->assertInternalType('string', $fake->readMapped('jsonlower'));
    }

    public function testShouldResolveValidCopyOfPhpObject()
    {
        $mappingManager = new MappingManager;
        $mappingManager->add(new Serialize);

        $data = new FakeClass('whatever');

        $fake = new Fake;
        $fake->phpobject = serialize($data);

        $this->assertInstanceOf('\Vegas\Tests\Db\FakeClass', $fake->readMapped('phpobject'));
        $this->assertEquals('whatever', $fake->readMapped('phpobject')->param);
        $this->assertEquals($data, $fake->readMapped('phpobject'));

        $this->assertNotSame($data, $fake->readMapped('phpobject'));
    }

    public function testShouldPrintDecimalValue()
    {
        $mappingManager = new MappingManager;
        $mappingManager->add(new Decimal);

        $fake = new Fake;
        $fake->currency = 987654.0321;

        $this->assertInternalType('string', $fake->readMapped('currency'));
        $this->assertEquals('987654.03', $fake->readMapped('currency'));

        $fake->currency = 7.1234E3;

        $this->assertInternalType('string', $fake->readMapped('currency'));
        $this->assertEquals('7123.40', $fake->readMapped('currency'));
    }
} 