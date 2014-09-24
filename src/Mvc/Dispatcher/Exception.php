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
namespace Vegas\Mvc\Dispatcher;

use Vegas\Exception as VegasException;

/**
 * Class Exception
 * @package Vegas\Dispatcher
 */
class Exception extends VegasException
{
    /**
     * Exception default message
     *
     * @var string
     */
    protected $message = 'Dispatcher error';
}
 