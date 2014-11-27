<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Test\Controllers\Backend;

use Vegas\Mvc\Controller\CrudAbstract;
use Vegas\Mvc\View;

class CrudController extends CrudAbstract
{
    protected $formName = 'Test\Forms\Fake';
    protected $modelName = 'Test\Models\Fake';

    public function initialize()
    {
        parent::initialize();
        $this->view->disableLevel(View::LEVEL_LAYOUT);
    }

    protected function afterCreate()
    {
        parent::afterCreate();

        $record = $this->scaffolding->getRecord();
        $record->after_create_content = 'afterCreate added content';
        $record->save();
    }

    protected function redirectAfterSave()
    {
        return $this->jsonResponse($this->scaffolding->getRecord()->getId());
    }

    protected function redirectAfterDelete()
    {
        return $this->jsonResponse();
    }
}