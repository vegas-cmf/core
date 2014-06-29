<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
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
        require_once TESTS_ROOT_DIR . '/fixtures/app/module/Test/forms/Fake.php';
    }

    public function testNotConfiguredCrud()
    {
        $crud = new Crud();

        try {
            $crud->initialize();
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Mvc\Controller\Crud\Exception\NotConfiguredException', $ex);
        }
    }

    public function testNew()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $form = new Fake();
        $content = $this->bootstrap->run('/test/crud/new');

        $this->assertEquals($form->get('fake_field')->render(), $content);
    }

    public function testNewResponse()
    {
        $form = new Fake();
        $this->assertEquals($form->get('fake_field')->render(), $this->di->get('response')->getContent());
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
        $id = $this->di->get('response')->getContent();

        $model = FakeModel::findById($id);

        $this->assertInstanceOf('\Test\Models\Fake', $model);
        $this->assertEquals(base64_encode(date('Y-m-d')), $model->fake_field);

        $model->delete();
    }

    public function testEdit()
    {
        $this->prepareFakeObject();

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $form = new Fake($this->model);
        $content = $this->bootstrap->run('/test/crud/edit/'.$this->model->getId());

        $this->assertEquals($form->get('fake_field')->render(), $content);
    }

    public function testEditResponse()
    {
        $this->prepareFakeObject();

        $form = new Fake($this->model);
        $this->assertEquals($form->get('fake_field')->render(), $this->di->get('response')->getContent());
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
        $this->assertNotEmpty($content);
    }

    public function testPostUpdateResponse()
    {
        $id = $this->di->get('response')->getContent();

        $model = FakeModel::findById($id);

        $this->assertInstanceOf('\Test\Models\Fake', $model);
        $this->assertEquals(base64_encode('foobar'), $model->fake_field);

        $model->delete();
    }

    public function testDelete()
    {
        $this->model = FakeModel::findFirst();

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->bootstrap->run('/test/crud/delete/'.$this->model->getId());

        $this->model = FakeModel::findFirst(array(array(
            'fake_field' => base64_encode('foobar')
        )));

        $this->assertFalse($this->model);
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