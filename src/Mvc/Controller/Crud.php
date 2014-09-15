<?php
/**
 * This file is part of Vegas package.
 * 
 * Default usage:
 * <code>
 * class MyController extends Controller\Crud {  
 *      protected $formName = 'My\Forms\My';    // default form used by CRUD
 *      protected $modelName = 'My\Models\My';  // default model used by CRUD
 * 
 *      public function initialize()
 *      {
 *          parent::initialize();
 *       
 *          // we can also add this event in the Module.php to the dispatcher
 *          $this->dispatcher->getEventsManager()->attach(
 *              Controller\Crud\Events::AFTER_CREATE, function() {
 *                  $this->response->redirect('user-admin/index');
 *              }
 *          );
 * 
 *          // attach more events
 *      }
 *
 *      // instead using eventManager you can use method like
 *      protected function afterCreate()
 *      {
 *          parent::afterCreate();
 *          $this->response->redirect('user-admin/index');
 *      }
 *
 *      // other actions
 * }
 * </code>
 * 
 * The list of fired events is located in \Vegas\Mvc\Controller\Crud\Events class.
 * 
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Mvc\Controller;

use Vegas\Exception;

/**
 * Class Crud
 * @package Vegas\Mvc\Controller
 */
class Crud extends ControllerAbstract
{
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
    public function initialize()
    {
        parent::initialize();

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
     * Displays form for new record
     *
     * @ACL(name="new", description="Create a new record")
     */
    public function newAction()
    {
        $this->beforeNew();

        $this->view->form = $this->scaffolding->getForm();

        $this->afterNew();
    }

    /**
     * Creates new record
     *
     * @ACL(name="create", inherit='new')
     */
    public function createAction()
    {
        $this->checkRequest();
        $this->beforeCreate();

        try {
            $this->beforeSave();

            $this->scaffolding->doCreate($this->request->getPost());

            $this->afterSave();

            $this->view->disable();
            $this->flash->success($this->successMessage);

            $this->afterCreate();
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());

            $this->afterCreateException();
        }

        $this->dispatcher->forward(array('action' => 'new'));
    }

    /**
     * Displays form for existing record
     *
     * @ACL(name="edit", description="Record edit")
     * @param $id
     */
    public function editAction($id)
    {
        $this->beforeEdit();

        $this->view->record = $this->scaffolding->doRead($id);

        $this->afterRead();

        $this->view->form =  $this->scaffolding->getForm($this->view->record);

        $this->afterEdit();
    }

    /**
     * Updates existing record indicated by its ID
     *
     * @ACL(name="update", inherit='edit')
     * @param $id
     */
    public function updateAction($id)
    {
        $this->checkRequest();
        $this->beforeUpdate();

        try {
            $this->beforeSave();

            $this->scaffolding->doUpdate($id, $this->request->getPost());

            $this->afterSave();

            $this->view->disable();
            $this->flash->success($this->successMessage);

            $this->afterUpdate();
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());

            $this->afterUpdateException();
        }

        $this->dispatcher->forward(array('action' => 'edit'));
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
     */
    public function deleteAction($id)
    {
        $this->view->disable();

        $this->beforeDelete();

        try {
            $this->scaffolding->doDelete($id);
            $this->flash->success($this->successMessage);
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());
        }

        $this->afterDelete();
    }

    /**
     * Method invoked on the beginning of the newAction.
     */
    protected function beforeNew()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_NEW, $this);
    }

    /**
     * Method invoked on the end of the newAction.
     */
    protected function afterNew()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_NEW, $this);
    }

    /**
     * Method invoked on the beginning of the createAction after checking request validity.
     */
    protected function beforeCreate()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_CREATE, $this);
    }

    /**
     * Method invoked on the end of the successful createAction before dispatcher forward.
     */
    protected function afterCreate()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_CREATE, $this);
    }

    /**
     * Method invoked on the end of the createAction failure before dispatcher forward.
     */
    protected function afterCreateException()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_CREATE_EXCEPTION, $this);
    }

    /**
     * Method invoked on the beginning of the editAction.
     */
    protected function beforeEdit()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_EDIT, $this);
    }

    /**
     * Method invoked after reading and setting $this->view->record variable but before creating $this->view->form.
     */
    protected function afterRead()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_READ, $this);
    }

    /**
     * Method invoked on the end of the editAction.
     */
    protected function afterEdit()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_EDIT, $this);
    }

    /**
     * Method invoked on the beginning of the updateAction after checking request validity.
     */
    protected function beforeUpdate()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_UPDATE, $this);
    }

    /**
     * Method invoked on the end of the successful updateAction before dispatcher forward.
     */
    protected function afterUpdate()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_UPDATE, $this);
    }

    /**
     * Method invoked on the end of the updateAction failure before dispatcher forward.
     */
    protected function afterUpdateException()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_UPDATE_EXCEPTION, $this);
    }

    /**
     * Method invoked on the beginning of the deleteAction.
     */
    protected function beforeDelete()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_DELETE, $this);
    }

    /**
     * Method invoked on the end of the deleteAction.
     */
    protected function afterDelete()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_DELETE, $this);
    }

    /**
     * Method invoked just before doUpdate/doCreate method in createAction and updateAction after calling
     * boforeCreate/beforeUpdate.
     */
    protected function beforeSave()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_SAVE, $this);
    }

    /**
     * Method invoked just after doUpdate/doCreate method in createAction and updateAction before success or failure
     * information.
     */
    protected function afterSave()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_SAVE, $this);
    }
}