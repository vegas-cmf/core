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

/**
 * Class Events
 * Event dispatcher events
 *
 * @package Vegas\Mvc\Controller\Crud
 */
class Events
{
    const AFTER_READ = 'crud:afterRead';

    const BEFORE_NEW = 'crud:beforeNew';
    const AFTER_NEW = 'crud:afterNew';

    const BEFORE_CREATE = 'crud:beforeCreate';
    const AFTER_CREATE = 'crud:afterCreate';
    const AFTER_CREATE_EXCEPTION = 'crud:afterCreateException';
    
    const BEFORE_UPDATE = 'crud:beforeUpdate';
    const AFTER_UPDATE = 'crud:afterUpdate';
    const AFTER_UPDATE_EXCEPTION = 'crud:afterUpdateException';
    
    const BEFORE_DELETE = 'crud:beforeDelete';
    const AFTER_DELETE = 'crud:afterDelete';
    
    const BEFORE_EDIT = 'crud:beforeEdit';
    const AFTER_EDIT = 'crud:afterEdit';
    
    const BEFORE_UPLOAD = 'crud:beforeUpload';
    const AFTER_UPLOAD = 'crud:afterUpload';

    const AFTER_SAVE = 'crud:afterSave';
    const BEFORE_SAVE = 'crud:beforeSave';
}
