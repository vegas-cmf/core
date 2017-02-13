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
namespace Vegas\Tests\Di;

use Vegas\Db\Exception\NoRequiredServiceException;
use Vegas\Di\Scaffolding;

class ScaffoldingTest extends \PHPUnit_Framework_TestCase
{
    protected $scaffolding;
    protected $record;
    
    protected function setUp()
    {
        $scaffolding = new Scaffolding(new Scaffolding\Adapter\Mongo());
        $scaffolding->setModelName('\Vegas\Tests\Stub\Models\FakeModel');
        $scaffolding->setFormName('\Vegas\Tests\Stub\Models\FakeForm');
        
        $this->scaffolding = $scaffolding;
        
        $record = new \Vegas\Tests\Stub\Models\FakeModel();
        $record->fake_field = 'test';
        $record->save();

        $record = new \Vegas\Tests\Stub\Models\FakeModel();
        $record->fake_field = 'test_query';
        $record->save();
        
        $this->record = $record;
    }

    public function testRequiredServicesVerification()
    {
        $di = \Phalcon\Di::getDefault();

        $emptyDI = new \Phalcon\Di\FactoryDefault();
        \Phalcon\Di::setDefault($emptyDI);

        $exception = null;
        try {
            new Scaffolding(new Scaffolding\Adapter\Mongo());
        } catch (NoRequiredServiceException $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Db\Exception\NoRequiredServiceException', $exception);

        //reverts DI
        \Phalcon\Di::setDefault($di);
    }
    
    public function testGetRecord()
    {
        $fakeModel = new \Vegas\Tests\Stub\Models\FakeModel();
        
        $record = $this->scaffolding->getRecord();
        $this->assertEquals($fakeModel, $record);
        
        $this->assertNotEquals($fakeModel, $this->record);
    }
    
    public function testGetForm()
    {
        $fakeForm = new \Vegas\Tests\Stub\Models\FakeForm();
        
        $this->assertEquals($fakeForm, $this->scaffolding->getForm());
        
        $emptyRecord = $this->scaffolding->getRecord();
        $scaffoldForm = $this->scaffolding->getForm($emptyRecord);
        
        $this->assertEquals($fakeForm, $scaffoldForm);
        
        $notEmptyForm = new \Vegas\Tests\Stub\Models\FakeForm($this->record);
        
        $this->assertNotEquals($scaffoldForm, $notEmptyForm);
    }
    
    public function testDoRead()
    {
        $record = $this->scaffolding->doRead($this->record->getId());
        $this->assertEquals($this->record->getId(), $record->getId());
        
        try {
            $this->scaffolding->doRead(new \MongoId());
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Di\Scaffolding\Exception\RecordNotFoundException', $ex);
        }
        
        try {
            $this->scaffolding->doRead('not_existing_id');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Di\Scaffolding\Exception\RecordNotFoundException', $ex);
        }
    }
    
    public function testDoCreate()
    {
        $values = array('fake_field' => 'test');
        
        $this->scaffolding->doCreate($values);
        $firstRecord = $this->scaffolding->getRecord();
        
        $this->scaffolding->doCreate($values);
        $secondRecord = $this->scaffolding->getRecord();
        
        $this->assertNotEquals($firstRecord->getId(), $secondRecord->getId());
    }
    
    public function testDoUpdate()
    {
        $values = array('fake_field' => 'testtest');

        $this->scaffolding->doUpdate($this->record->getId(), $values);
        $updatedRecordId = $this->scaffolding->getRecord()->getId();
        
        $this->assertEquals($this->record->getId(), $updatedRecordId);
    }
    
    public function testDoDelete()
    {
        $this->scaffolding->doDelete($this->record->getId());
        
        try {
            $this->scaffolding->doDelete($this->record->getId());
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Di\Scaffolding\Exception\RecordNotFoundException', $ex);
        }
    }

    public function testShouldReturnValidPagination()
    {
        $pagination = $this->scaffolding->doPaginate();

        $this->assertInstanceOf('\Vegas\Paginator\Adapter\Mongo', $pagination);

        $results = $pagination->getResults();

        $this->assertNotCount(0, $results);
        $this->assertInstanceOf('\Vegas\Tests\Stub\Models\FakeModel', $results[0]);
    }

    public function testShouldReturnFilteredPagination()
    {
        $this->scaffolding->setQuery([
            'fake_field' => 'test_query'
        ]);

        $pagination = $this->scaffolding->doPaginate();
        $results = $pagination->getResults();

        $this->assertNotCount(0, $results);
        $this->assertEquals('test_query', $results[0]->fake_field);
    }
}
