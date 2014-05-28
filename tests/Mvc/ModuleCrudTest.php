<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Tests\Mvc;

use Phalcon\DI;
use Test\Forms\Fake;
use Test\Models\Fake As FakeModel;
use Vegas\Mvc\Controller\Crud;
use Vegas\Tests\App\TestCase;

class ModuleWithCrudTest extends TestCase
{
    protected $model;

    public function setUp()
    {
        parent::setUp();

        $this->model = FakeModel::findFirst(array(array(
            'fake_field' => base64_encode(date('Y-m-d'))
        )));
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
        $this->assertEquals('', $content);
    }

    public function testNotPostCreateResponse()
    {
        $this->assertEquals('500 Application error', $this->di->get('response')->getHeaders()->get('Status'));
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

        $this->model = $model;
    }

    public function testEdit()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $form = new Fake($this->model);
        $content = $this->bootstrap->run('/test/crud/edit/'.$this->model->getId());

        $this->assertEquals($form->get('fake_field')->render(), $content);
    }

    public function testEditResponse()
    {
        $form = new Fake($this->model);
        $this->assertEquals($form->get('fake_field')->render(), $this->di->get('response')->getContent());
    }

    public function testNotPostUpdate()
    {
        $content = $this->bootstrap->run('/test/crud/update/'.$this->model->getId());
        $this->assertEquals('', $content);
    }

    public function testNotPostUpdateResponse()
    {
        $this->assertEquals('500 Application error', $this->di->get('response')->getHeaders()->get('Status'));
    }

    public function testPostUpdate()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['fake_field'] = base64_encode('foobar');

        $content = $this->bootstrap->run('/test/crud/update/'.$this->model->getId());
        $this->assertNotEmpty($content);
    }

    public function testPostUpdateResponse()
    {
        $id = $this->di->get('response')->getContent();
;
        $model = FakeModel::findById($id);

        $this->assertInstanceOf('\Test\Models\Fake', $model);
        $this->assertEquals(base64_encode('foobar'), $model->fake_field);
        $this->assertFalse($this->model);
    }

    public function testDelete()
    {
        $model = FakeModel::findFirst(array(array(
            'fake_field' => base64_encode('foobar')
        )));

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->bootstrap->run('/test/crud/delete/'.$model->getId());

        $model = FakeModel::findFirst(array(array(
            'fake_field' => base64_encode('foobar')
        )));

        $this->assertFalse($model);
    }
} 