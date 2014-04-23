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

namespace Vegas\Dispatcher\Exception;

use Vegas\Dispatcher\Exception as Exception;

/**
 * Class CannotHandleErrorException
 * @package Vegas\Dispatcher\Exception
 */
class CannotHandleErrorException extends Exception
{
    protected $message = 'Dispatcher cannot handle this exception.';
}
 