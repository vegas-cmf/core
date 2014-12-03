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
 
namespace Foo\Controllers\Backend;

use Vegas\Mvc\Controller\CrudAbstract;

class CrudController extends CrudAbstract
{
    protected $formName = 'Test\Forms\Fake';
    protected $modelName = 'Test\Models\Fake';
} 