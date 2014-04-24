<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Tests\Db;

use Phalcon\DI;
use Vegas\Db\Decorator\CollectionAbstract;
use Vegas\Db\Mapping\Json;
use Vegas\Db\MappingManager;

class Fake extends CollectionAbstract
{
    public function getSource()
    {
        return 'fake';
    }

    protected $mappings = array(
        'somedata'  =>  'json',
        'somecamel' =>  'camelize'
    );
}

class MappingTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveMappings()
    {
        DI::getDefault()->get('mongo')->selectCollection('fake')->remove(array());

        //define mappings
        $mappingManager = new MappingManager();
        $mappingManager->add(new Json());
        $mappingManager->add('\Vegas\Db\Mapping\Camelize');

        $this->assertNotEmpty($mappingManager->find('json'));
        $this->assertInstanceOf('\Vegas\Db\MappingInterface', $mappingManager->find('json'));

        $this->assertNotEmpty($mappingManager->find('camelize'));
        $this->assertInstanceOf('\Vegas\Db\MappingInterface', $mappingManager->find('camelize'));

        $fake = new Fake();
        $fake->somedata = json_encode(array(1,2,3,4,5,6));
        $nonCamelText = 'this_is_non_camel_case_text';
        $fake->somecamel = $nonCamelText;
        $this->assertTrue($fake->save());

        $fakeDoc = Fake::findFirst();

        print_r($fakeDoc->somedata);
        print_r($fakeDoc->somecamel);
        $r = new \ReflectionClass($fakeDoc);
        $n = $r->newInstance();
        var_dump($n->somedata);

        $this->assertInternalType('array', $fakeDoc->readAttribute('somedata'));
        $this->assertEquals(\Phalcon\Text::camelize($nonCamelText), $fakeDoc->readAttribute('somecamel'));
    }

} 