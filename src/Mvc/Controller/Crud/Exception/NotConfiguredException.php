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
namespace Vegas\Mvc\Controller\Crud\Exception;

use Vegas\Mvc\Controller\Exception as Exception;

/**
 * Class NotConfiguredException
 * @package Vegas\Mvc\Controller\Crud\Exception
 */
class NotConfiguredException extends Exception
{
    /**
     * Exception default message
     *
     * @var string
     */
    protected $message = "CRUD is not configured.";
}
