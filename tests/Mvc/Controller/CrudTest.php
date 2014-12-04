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
use Test\Models\Fake As FakeModel;
use Vegas\Mvc\Controller\Crud;
use Vegas\Tests\App\TestCase;

class CrudTest extends TestCase
{
    protected $model;

    public function setUp()
    {
        parent::setUp();
        $config = DI::getDefault()->get('config');
        require_once $config->application->moduleDir . '/Test/forms/Fake.php';
    }

    public function testNotConfiguredCrud()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $content = $this->bootstrap->run('/test/brokencrud/new');
        $this->assertContains('500', $content);
        $this->assertContains('CRUD is not configured.', $content);
    }

    public function testNew()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $form = new Fake();

        $content = $this->bootstrap->run('/test/crud/new');

        $this->assertContains($form->get('fake_field')->render(['class' => ' form-control']), $content);
        $this->assertContains('<form action="/test/crud/create/" method="POST" role="form">', $content);
    }

    public function testNotPostCreate()
    {
        $content = $this->bootstrap->run('/test/crud/create');

        $this->assertContains('500', $content);
        $this->assertContains('This is not a POST request!', $content);
    }

    public function testNotPostCreateResponse()
    {
        $this->assertEquals('500 This is not a POST request!', $this->di->get('response')->getHeaders()->get('Status'));
    }

    public function testPostCreate()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['fake_field'] = base64_encode(date('Y-m-d'));

        $content = $this->bootstrap->run('/test/crud/create');
        $this->assertNotEmpty($content);
    }

    public function testPostCreateResponse()
    {
        $contentArray = json_decode($this->di->get('response')->getContent(), true);

        $model = FakeModel::findById($contentArray['$id']);

        $this->assertInstanceOf('\Test\Models\Fake', $model);
        $this->assertEquals(base64_encode(date('Y-m-d')), $model->fake_field);
        $this->assertEquals('afterCreate added content', $model->after_create_content);

        $model->delete();
    }

    public function testPostCreateException()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['fake_field'] = '';

        $content = $this->bootstrap->run('/test/crud/create');
        $this->assertContains('Field fake_field is required', $content);
    }

    public function testEdit()
    {
        $this->prepareFakeObject();

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $form = new Fake($this->model);
        $content = $this->bootstrap->run('/test/crud/edit/'.$this->model->getId());

        $this->assertContains($form->get('fake_field')->render(['class' => ' form-control']), $content);
        $this->assertContains('<form action="/test/crud/update/'.$this->model->getId().'" method="POST" role="form">', $content);
    }

    public function testNotPostUpdate()
    {
        $this->prepareFakeObject();

        $content = $this->bootstrap->run('/test/crud/update/'.$this->model->getId());

        $this->assertContains('500', $content);
        $this->assertContains('This is not a POST request!', $content);
    }

    public function testNotPostUpdateResponse()
    {
        $this->assertContains('500', $this->di->get('response')->getHeaders()->get('Status'));
        $this->assertContains('This is not a POST request!', $this->di->get('response')->getHeaders()->get('Status'));
    }

    public function testPostUpdate()
    {
        $this->prepareFakeObject();

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['fake_field'] = base64_encode('foobar');

        $content = $this->bootstrap->run('/test/crud/update/'.$this->model->getId());
        $this->assertEquals(json_encode($this->model->getId()), $content);
    }

    public function testPostUpdateResponse()
    {
        $contentArray = json_decode($this->di->get('response')->getContent(), true);

        $model = FakeModel::findById($contentArray['$id']);

        $this->assertInstanceOf('\Test\Models\Fake', $model);
        $this->assertEquals(base64_encode('foobar'), $model->fake_field);

        $model->delete();
    }

    public function testPostUpdateException()
    {
        $this->prepareFakeObject();

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['fake_field'] = '';

        $content = $this->bootstrap->run('/test/crud/update/'.$this->model->getId());
        $this->assertContains('Field fake_field is required', $content);
    }

    public function testIndex()
    {
        $this->prepareFakeObject();

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $content = $this->bootstrap->run('/test/crud/index/');

        $this->assertContains('<th>Fake field index</th>', $content);
        $this->assertContains($this->model->fake_field, $content);
    }

    public function testShow()
    {
        $this->prepareFakeObject();

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $content = $this->bootstrap->run('/test/crud/show/'.$this->model->getId());

        $this->assertContains('<th>Fake field</th>', $content);
        $this->assertContains($this->model->fake_field, $content);
    }

    public function testDelete()
    {
        $this->model = FakeModel::findFirst();

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->bootstrap->run('/test/crud/delete/'.$this->model->getId());

        $this->model = FakeModel::findById($this->model->getId());

        $this->assertFalse($this->model);
    }

    public function testDeleteException()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $content = $this->bootstrap->run('/test/crud/delete/RanDoMn0t1D4sUR3');

        $this->assertContains('500', $content);
        $this->assertContains('Invalid object ID', $content);
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
}