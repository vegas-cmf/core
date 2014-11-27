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
namespace Vegas\Mvc\Controller\Crud;

trait HooksTrait
{
    /**
     * Redirect to specific action in the same controller.
     *
     * @param $action
     * @return mixed
     */
    protected function redirectToAction($action)
    {
        return $this->response->redirect([
            'for' => $this->router->getMatchedRoute()->getName(),
            'action' => $action
        ]);
    }
    /**
     * Method invoked on the beginning of the newAction.
     */
    protected function beforeNew()
    {
        $this->dispatcher->getEventsManager()->fire(Events::BEFORE_NEW, $this);
    }
    /**
     * Method invoked on the end of the newAction.
     */
    protected function afterNew()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_NEW, $this);
    }
    /**
     * Method invoked on the beginning of the createAction after checking request validity.
     */
    protected function beforeCreate()
    {
        $this->dispatcher->getEventsManager()->fire(Events::BEFORE_CREATE, $this);
        $this->beforeSave();
    }
    /**
     * Method invoked on the end of the successful createAction before picking view.
     */
    protected function afterCreate()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_CREATE, $this);
        return $this->afterSave();
    }
    /**
     * Method invoked on the end of the createAction failure before picking view.
     */
    protected function afterCreateException()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_CREATE_EXCEPTION, $this);
        return $this->redirectToAction('index');
    }
    /**
     * Method invoked on the beginning of the editAction, after setting $this->view->record.
     */
    protected function beforeEdit()
    {
        $this->dispatcher->getEventsManager()->fire(Events::BEFORE_EDIT, $this);
    }

    /**
     * Method invoked before reading and setting $this->view->record variable.
     */
    protected function beforeRead()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_READ, $this);
    }

    /**
     * Method invoked after reading and setting $this->view->record variable but before creating $this->view->form.
     */
    protected function afterRead()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_READ, $this);
    }
    /**
     * Method invoked on the end of the editAction.
     */
    protected function afterEdit()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_EDIT, $this);
    }
    /**
     * Method invoked on the beginning of the updateAction after checking request validity.
     */
    protected function beforeUpdate()
    {
        $this->dispatcher->getEventsManager()->fire(Events::BEFORE_UPDATE, $this);
        $this->beforeSave();
    }
    /**
     * Method invoked on the end of the successful updateAction before picking view.
     */
    protected function afterUpdate()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_UPDATE, $this);
        return $this->afterSave();
    }
    /**
     * Method invoked on the end of the updateAction failure before picking view.
     */
    protected function afterUpdateException()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_UPDATE_EXCEPTION, $this);
        return $this->redirectToAction('index');
    }
    /**
     * Method invoked on the beginning of the deleteAction.
     */
    protected function beforeDelete()
    {
        $this->dispatcher->getEventsManager()->fire(Events::BEFORE_DELETE, $this);
    }
    /**
     * Method invoked on the end of the deleteAction.
     */
    protected function afterDelete()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_DELETE, $this);
        return $this->redirectToAction('index');
    }
    /**
     * Method invoked on the end of the deleteAction failure before picking view.
     */
    protected function afterDeleteException()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_DELETE_EXCEPTION, $this);
        return $this->redirectToAction('index');
    }
    /**
     * Method invoked just before doUpdate/doCreate method in createAction and updateAction after calling
     * boforeCreate/beforeUpdate.
     */
    protected function beforeSave()
    {
        $this->dispatcher->getEventsManager()->fire(Events::BEFORE_SAVE, $this);
    }
    /**
     * Method invoked just after doUpdate/doCreate method in createAction and updateAction before success information.
     */
    protected function afterSave()
    {
        $this->dispatcher->getEventsManager()->fire(Events::AFTER_SAVE, $this);
        return $this->redirectToAction('index');
    }
}