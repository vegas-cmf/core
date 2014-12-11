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
namespace Vegas\Tests\Mvc\Controller;

use Phalcon\DI;
use Test\Forms\Fake;
use Test\Models\Fake as FakeModel;
use Vegas\Mvc\Controller\Crud;
use Vegas\Test\TestCase;

class CrudTest extends TestCase
{
    protected $model;

    public function setUp()
    {
        parent::setUp();
        $config = DI::getDefault()->get('config');
        require_once $config->application->moduleDir . '/Test/forms/Fake.php';
        $this->prepareFakeObject();
    }

    private function prepareFakeObject()
    {
        $this->model = FakeModel::findFirst(array(array(
            'fake_field' => base64_encode(date('Y-m-d'))
        )));

        if (!$this->model) {
            $this->model =  new FakeModel();
            $this->model->fake_field = base64_encode(date('Y-m-d'));
            $this->model->save();
        }
    }

    public function testNotConfiguredCrud()
    {
        $this->request()->setRequestMethod('GET');

        $content = $response = $this->handleUri('/test/brokencrud/new')->getContent();
        $this->assertContains('500', $content);
        $this->assertContains('CRUD is not configured.', $content);
    }

    public function testNew()
    {
        $this->request()->setRequestMethod('GET');

        $form = new Fake();

        $content = $this->handleUri('/test/crud/new')->getContent();

        $this->assertContains($form->get('fake_field')->render(['class' => ' form-control']), $content);
        $this->assertContains('<form action="/test/crud/create/" method="POST" role="form">', $content);
    }

    public function testNotPostCreate()
    {
        $content = $this->handleUri('/test/crud/create')->getContent();

        $this->assertContains('500', $content);
        $this->assertContains('This is not a POST request!', $content);
    }

    public function testNotPostCreateResponse()
    {
        $response = $this->handleUri('/test/crud/create');
        $this->assertEquals('500 This is not a POST request!', $response->getHeaders()->get('Status'));
    }

    public function testPostCreate()
    {
        $this->request()
            ->setRequestMethod('POST')
            ->setPost('fake_field', base64_encode(date('Y-m-d')));

        $content = $this->handleUri('/test/crud/create')->getContent();
        $this->assertNotEmpty($content);
    }

    public function testPostCreateResponse()
    {
        $this->request()
            ->setRequestMethod('POST')
            ->setPost('fake_field', base64_encode(date('Y-m-d')));
        $response = $this->handleUri('/test/crud/create');

        $contentArray = json_decode($response->getContent(), true);

        $model = FakeModel::findById($contentArray['$id']);

        $this->assertInstanceOf('\Test\Models\Fake', $model);
        $this->assertEquals(base64_encode(date('Y-m-d')), $model->fake_field);
        $this->assertEquals('afterCreate added content', $model->after_create_content);

        $model->delete();
    }

    public function testPostCreateException()
    {
        $this->request()
            ->setRequestMethod('POST')
            ->setPost('fake_field', '');

        $content = $this->handleUri('/test/crud/create')->getContent();
        $this->assertContains('Field fake_field is required', $content);
    }

    public function testEdit()
    {
        $this->request()->setRequestMethod('GET');

        $form = new Fake($this->model);
        $content = $this->handleUri('/test/crud/edit/'.$this->model->getId())->getContent();

        $this->assertContains($form->get('fake_field')->render(['class' => ' form-control']), $content);
        $this->assertContains('<form action="/test/crud/update/'.$this->model->getId().'" method="POST" role="form">', $content);
    }

    public function testNotPostUpdate()
    {
        $content = $this->handleUri('/test/crud/update/'.$this->model->getId())->getContent();

        $this->assertContains('500', $content);
        $this->assertContains('This is not a POST request!', $content);
    }

    public function testNotPostUpdateResponse()
    {
        $response = $this->handleUri('/test/crud/update/'.$this->model->getId());
        $this->assertContains('500', $response->getHeaders()->get('Status'));
        $this->assertContains('This is not a POST request!', $response->getHeaders()->get('Status'));
    }

    public function testPostUpdate()
    {
        $this->request()
            ->setRequestMethod('POST')
            ->setPost('fake_field', base64_encode('foobar'));

        $content = $this->handleUri('/test/crud/update/'.$this->model->getId())->getContent();
        $this->assertEquals(json_encode($this->model->getId()), $content);
    }

    public function testPostUpdateResponse()
    {
        $this->request()
            ->setRequestMethod('POST')
            ->setPost('fake_field', base64_encode('foobar'));

        $response = $this->handleUri('/test/crud/update/'.$this->model->getId());
        $contentArray = json_decode($response->getContent(), true);

        $model = FakeModel::findById($contentArray['$id']);

        $this->assertInstanceOf('\Test\Models\Fake', $model);
        $this->assertEquals(base64_encode('foobar'), $model->fake_field);

        $model->delete();
    }

    public function testPostUpdateException()
    {
        $this->request()
            ->setRequestMethod('POST')
            ->setPost('fake_field', '');

        $content = $this->handleUri('/test/crud/update/'.$this->model->getId())->getContent();
        $this->assertContains('Field fake_field is required', $content);
    }

    public function testIndex()
    {
        $this->request()->setRequestMethod('GET');

        $content = $this->handleUri('/test/crud/index/')->getContent();

        $this->assertContains('<th>Fake field index</th>', $content);
        $this->assertContains($this->model->fake_field, $content);
    }

    public function testShow()
    {
        $this->request()->setRequestMethod('GET');

        $content = $this->handleUri('/test/crud/show/'.$this->model->getId())->getContent();

        $this->assertContains('<th>Fake field</th>', $content);
        $this->assertContains($this->model->fake_field, $content);
    }

    public function testDelete()
    {
        $this->model = FakeModel::findFirst();

        $this->request()->setRequestMethod('GET');

        $this->handleUri('/test/crud/delete/'.$this->model->getId());
        $this->model = FakeModel::findById($this->model->getId());

        $this->assertFalse($this->model);
    }

    public function testDeleteException()
    {
        $this->request()->setRequestMethod('GET');

        $content = $this->handleUri('/test/crud/delete/RanDoMn0t1D4sUR3')->getContent();

        $this->assertContains('500', $content);
        $this->assertContains('Invalid object ID', $content);
    }
}