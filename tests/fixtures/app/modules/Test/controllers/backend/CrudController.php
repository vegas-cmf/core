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

    protected $showFields = [
        'fake_field' => 'Fake field',
        'created_at' => 'Created at'
    ];
    protected $indexFields = [
        'fake_field' => 'Fake field index',
        'created_at' => 'Created at index'
    ];

    public function initialize()
    {
        parent::initialize();
        $this->view->disableLevel(View::LEVEL_LAYOUT);
    }

    protected function afterCreate()
    {
        $record = $this->scaffolding->getRecord();
        $record->after_create_content = 'afterCreate added content';
        $record->save();

        return parent::afterCreate();
    }

    protected function afterSave()
    {
        return $this->jsonResponse($this->scaffolding->getRecord()->getId());
    }

    protected function afterDelete()
    {
        return $this->jsonResponse();
    }
}