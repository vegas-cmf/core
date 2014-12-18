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
namespace Vegas\DI;

use Vegas\DI\Scaffolding\AdapterInterface;
use Vegas\DI\Scaffolding\Exception\DeleteFailureException;
use Vegas\DI\Scaffolding\Exception\InvalidFormException;

/**
 * Class Scaffolding
 *
 * Usage
 * <code>
 * class MyController extends Controller\Crud {
 *      protected $formName = 'My\Forms\My';    // default form used by CRUD
 *      protected $modelName = 'My\Models\My';  // default model used by CRUD
 *      protected $fields = [
 *          'name' => 'Name',
 *          'url' => 'Url'
 *      ]; // default field set for index and show actions (all fields will be echoed via readMapped() method)
 *  }
 * </code>
 *
 * @package Vegas\DI
 */
class Scaffolding implements ScaffoldingInterface
{
    /**
     * Dependency injector
     *
     * @var
     */
    protected $di;

    /**
     * Record object
     *
     * @var
     */
    protected $record;

    /**
     * Form object
     *
     * @var
     */
    protected $form;

    /**
     * Scaffolding adapter model
     *
     * @var Scaffolding\AdapterInterface
     */
    protected $adapter;

    /**
     * Model class name
     *
     * @var string
     */
    protected $modelName;

    /**
     * Form class name
     *
     * @var string
     */
    protected $formName;

    /**
     * {@inheritdoc}
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->adapter->setScaffolding($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecord()
    {
        if (empty($this->record)) {
            $this->record = new $this->modelName();
        }
        return $this->record;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm($entity = null)
    {
        if (empty($this->form)) {
            $this->form = new $this->formName($entity);
        }
        return $this->form;
    }

    /**
     * Sets form instance
     *
     * @param $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormName($name)
    {
        $this->formName = $name;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setModelName($name)
    {
        $this->modelName = $name;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function doRead($id)
    {
        $this->record = $this->adapter->retrieveOne($id);
        return $this->record;
    }

    /**
     * {@inheritdoc}
     */
    public function doPaginate($page = 1, $limit = 10)
    {
        return $this->adapter->getPaginator($page, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function doCreate(array $values)
    {
        $this->record = new $this->modelName();
        return $this->processForm($values);
    }

    /**
     * {@inheritdoc}
     */
    public function doUpdate($id, array $values)
    {
        $this->record = $this->doRead($id);
        return $this->processForm($values);
    }

    /**
     * {@inheritdoc}
     */
    public function doDelete($id)
    {
        $this->record = $this->doRead($id);

        try {
            return $this->record->delete();
        } catch (\Exception $e) {
            throw new DeleteFailureException($e->getMessage());
        }
    }

    /**
     * Processes form with provided values
     *
     * @param $values
     * @return mixed
     * @throws Scaffolding\Exception\InvalidFormException
     * @internal
     */
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

    /**
     * {@inheritdoc}
     */
    public function getAdapter() {
        return $this->adapter;
    }
}
