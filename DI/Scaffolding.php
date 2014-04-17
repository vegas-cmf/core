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
namespace Vegas\DI;

use Vegas\DI\Scaffolding\Exception\DeleteFailureException;
use Vegas\DI\Scaffolding\Exception\InvalidFormException;

class Scaffolding implements \Vegas\DI\ScaffoldingInterface
{
    protected $di;
    protected $record;
    protected $form;
    protected $adapter;
    
    protected $modelName;
    protected $formName;
    
    public function __construct(\Vegas\DI\Scaffolding\AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->adapter->setScaffolding($this);
    }

    public function getRecord()
    {
        if (empty($this->record)) {
            $this->record = new $this->modelName();
        }
        return $this->record;
    }
    
    public function getForm($entity = null)
    {
        if (empty($this->form)) {
            $this->form = new $this->formName($entity);
        }
        return $this->form;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }
    
    public function setFormName($name)
    {
        $this->formName = $name;
        return $this;
    }
    
    public function setModelName($name)
    {
        $this->modelName = $name;
        return $this;
    }

    public function doRead($id)
    {
        $this->record = $this->adapter->retrieveOne($id);
        return $this->record;
    }
    
    public function doCreate(array $values)
    {
        $this->record = new $this->modelName();
        return $this->processForm($values);
    }
    
    public function doUpdate($id, array $values)
    {
        $this->record = $this->adapter->retrieveOne($id);
        return $this->processForm($values);
    }
    
    public function doDelete($id)
    {
        $this->record = $this->adapter->retrieveOne($id);
        
        try {
            return $this->record->delete();
        } catch (\Exception $e) {
            throw new DeleteFailureException($e->getMessage());
        }
    }
    
    private function processForm($values)
    {
        $form = $this->getForm();
        $form->bind($values, $this->record);
        
        if ($form->isValid()) {
            return $this->record->save();
        } 
        
        $this->form = $form;
        throw new InvalidFormException();
    }
    
    public function getAdapter() {
        return $this->adapter;
    }
}
