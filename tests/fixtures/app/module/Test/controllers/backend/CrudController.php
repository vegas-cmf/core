<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Test\Controllers\Backend;

use Vegas\Mvc\Controller\Crud;

class CrudController extends Crud
{
    protected $formName = 'Test\Forms\Fake';
    protected $modelName = 'Test\Models\Fake';

    public function initialize()
    {
        parent::initialize();

        $this->dispatcher->getEventsManager()->attach(Crud\Events::AFTER_CREATE, $this->printAfterSuccess());
        $this->dispatcher->getEventsManager()->attach(Crud\Events::AFTER_UPDATE, $this->printAfterSuccess());
    }

    private function printAfterSuccess()
    {
        // for testing purposes only
        return function() {
            echo $this->scaffolding->getRecord()->getId();
        };
    }
} 