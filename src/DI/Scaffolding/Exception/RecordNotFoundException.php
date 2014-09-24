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
namespace Vegas\DI\Scaffolding\Exception;

use Vegas\DI\Scaffolding\Exception;

/**
 * Class RecordNotFoundException
 * @package Vegas\DI\Scaffolding\Exception
 */
class RecordNotFoundException extends Exception
{
    /**
     * Exception default message
     */
    protected $message = 'Record does not exist.';

    /**
     * Exception default code
     */
    protected $code = 404;
}
