<?php
/**
 * This file is part of Vegas package.
 *
 * Default usage:
 * <code>
 * class MyController extends Controller\Crud {
 *      protected $formName = 'My\Forms\My';    // default form used by CRUD
 *      protected $modelName = 'My\Models\My';  // default model used by CRUD
 * }
 * </code>
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Mvc\Controller;

use Vegas\Exception;
use Vegas\Mvc\Controller\Crud\HooksTrait;
use Vegas\Mvc\ControllerAbstract;
use Vegas\Mvc\View;

/**
 * Class Crud
 * @package Vegas\Mvc\Controller
 */
abstract class CrudAbstract extends ControllerAbstract
{
    /**
     * Controller initialization block
     */
    public function initialize()
    {
        parent::initialize();

        if ($this->view instanceof \Vegas\Mvc\View && !$this->view->existsForCurrentAction()) {
            $templatePath = implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'Crud','']);

            $view = $this->getDI()->get('view');
            $view->setViewsDir($templatePath);
            $view->setControllerViewPath('views');

            $this->view = $view;
        }
    }

    use HooksTrait;

    /**
     * Default success message
     *
     * @var string
     */
    protected $successMessage = 'Action has been successful.';

    /**
     * Form class name
     *
     * @var string
     */
    protected $formName;

    /**
     * Model class name
     *
     * @var string
     */
    protected $modelName;

    /**
     * Initializes scaffolding
     *
     * @throws Crud\Exception\NotConfiguredException
     */
    private function initializeScaffolding()
    {
        if (!$this->isConfigured()) {
            throw new Crud\Exception\NotConfiguredException();
        }

        $this->scaffolding->setModelName($this->modelName);
        $this->scaffolding->setFormName($this->formName);
    }

    /**
     * @return bool
     * @internal
     */
    private function isConfigured()
    {
        return ($this->di->has('scaffolding') && !empty($this->modelName) && !empty($this->formName));
    }

    /**
     * Display records list.
     *
     * @ACL(name="new", description="Create a new record")
     */
    public function indexAction()
    {
        $this->initializeScaffolding();
    }

    /**
     * Display record details.
     *
     * @ACL(name="new", description="Create a new record")
     */
    public function showAction($id)
    {
        $this->initializeScaffolding();

        $this->beforeRead();
        $this->view->record = $this->scaffolding->doRead($id);
        $this->afterRead();
    }

    /**
     * Displays form for new record
     *
     * @ACL(name="new", description="Create a new record")
     */
    public function newAction()
    {
        $this->initializeScaffolding();

        $this->beforeNew();
        $this->view->form = $this->scaffolding->getForm();
        $this->afterNew();
    }

    /**
     * Creates new record
     *
     * @ACL(name="create", inherit='new')
     * @return mixed
     */
    public function createAction()
    {
        $this->initializeScaffolding();
        $this->checkRequest();

        try {
            $this->beforeCreate();
            $this->scaffolding->doCreate($this->request->getPost());
            $this->flash->success($this->successMessage);
            return $this->afterCreate();
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());
            $this->afterCreateException();
        }

        return $this->dispatcher->forward(['action' => 'new']);
    }

    /**
     * Displays form for existing record
     *
     * @ACL(name="edit", description="Record edit")
     * @param $id
     */
    public function editAction($id)
    {
        $this->initializeScaffolding();

        $this->beforeRead();
        $this->view->record = $this->scaffolding->doRead($id);
        $this->afterRead();

        $this->beforeEdit();
        $this->view->form =  $this->scaffolding->getForm($this->view->record);
        $this->afterEdit();
    }

    /**
     * Updates existing record indicated by its ID
     *
     * @ACL(name="update", inherit='edit')
     * @param $id
     * @return mixed
     */
    public function updateAction($id)
    {
        $this->initializeScaffolding();
        $this->checkRequest();

        try {
            $this->beforeRead();
            $this->view->record = $this->scaffolding->doRead($id);
            $this->afterRead();

            $this->beforeUpdate();
            $this->scaffolding->doUpdate($id, $this->request->getPost());
            $this->flash->success($this->successMessage);
            return $this->afterUpdate();
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());
            $this->afterUpdateException();
        }

        return $this->dispatcher->forward(['action' => 'edit']);
    }

    /**
     * Checks if request was send using POST method
     *
     * @throws Crud\Exception\PostRequiredException
     */
    protected function checkRequest()
    {
        if (!$this->request->isPost()) {
            throw new Crud\Exception\PostRequiredException();
        }
    }

    /**
     * Deletes existing record by its ID
     *
     * @ACL(name="delete", description="Delete a record")
     * @param $id
     * @return mixed
     */
    public function deleteAction($id)
    {
        $this->initializeScaffolding();

        try {
            $this->beforeRead();
            $this->view->record = $this->scaffolding->doRead($id);
            $this->afterRead();

            $this->beforeDelete();
            $this->scaffolding->doDelete($id);
            $this->flash->success($this->successMessage);
            return $this->afterDelete();
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());
            return $this->afterDeleteException();
        }
    }
}