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
 *      // other actions
 * }
 * </code>
 * 
 * The list of fired events is located in \Vegas\Mvc\Controller\Crud\Events class.
 * 
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Mvc\Controller;

use Vegas\Exception;

class Crud extends ControllerAbstract
{
    protected $successMessage = 'Action has been successful.';
    protected $formName;
    protected $modelName;

    public function initialize()
    {
        parent::initialize();
        
        if (!$this->isConfigured()) {
            throw new Crud\Exception\NotConfiguredException();
        }
        
        $this->scaffolding->setModelName($this->modelName);
        $this->scaffolding->setFormName($this->formName);
    }
    
    private function isConfigured()
    { 
       return ($this->di->has('scaffolding') && !empty($this->modelName) && !empty($this->formName));
    }

    /**
     * @ACL(name="new", description="Create a new record")
     */
    public function newAction()
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_NEW, $this);
        $this->view->form = $this->scaffolding->getForm();
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_NEW, $this);
    }

    /**
     * @ACL(name="create", inherit='new')
     */
    public function createAction()
    {
        $this->checkRequest();

        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_CREATE, $this);
        
        try {
            $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_SAVE, $this);
            $this->scaffolding->doCreate($this->request->getPost());
            $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_SAVE, $this);
            $this->view->disable();
            $this->flash->success($this->successMessage);
            $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_CREATE, $this);
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());
            $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_CREATE_EXCEPTION, $this);
        }
        
        $this->dispatcher->forward(array('action' => 'new'));
    }

    /**
     * @ACL(name="edit", description="Record edit")
     * @param $id
     */
    public function editAction($id)
    {
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_EDIT, $this);
        $this->view->record = $this->scaffolding->doRead($id);
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_READ, $this);
        $this->view->form =  $this->scaffolding->getForm($this->view->record);
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_EDIT, $this);
    }

    /**
     * @ACL(name="update", inherit='edit')
     * @param $id
     */
    public function updateAction($id)
    {
        $this->checkRequest();

        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_UPDATE, $this);
        
        try {
            $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_SAVE, $this);
            $this->scaffolding->doUpdate($id, $this->request->getPost());
            $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_SAVE, $this);
            $this->view->disable();
            $this->flash->success($this->successMessage);
            $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_UPDATE, $this);
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());
            $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_UPDATE_EXCEPTION, $this);
        }
        
        $this->dispatcher->forward(array('action' => 'edit'));
    }
    
    private function checkRequest()
    {
        if (!$this->request->isPost()) {
            throw new Crud\Exception\PostRequiredException();
        }
    }
    
    /**
     * @ACL(name="delete", description="Delete a record")
     * @param $id
     */
    public function deleteAction($id)
    {
        $this->view->disable();
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_DELETE, $this);
        
        try {
            $this->scaffolding->doDelete($id);
            $this->flash->success($this->successMessage); 
        } catch (Exception $e) {
            $this->flash->error($e->getMessage());
        }
        
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_DELETE, $this);
    }
}