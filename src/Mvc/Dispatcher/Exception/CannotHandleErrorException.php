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
namespace Vegas\Mvc\Dispatcher\Exception;

use Vegas\Mvc\Dispatcher\Exception as Exception;

/**
 * Class CannotHandleErrorException
 * @package Vegas\Dispatcher\Exception
 */
class CannotHandleErrorException extends Exception
{
    /**
     * Exception default message
     * 
     * @var string
     */
    protected $message = 'Dispatcher cannot handle this exception.';
}
