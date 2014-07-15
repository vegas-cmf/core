<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\DI\Scaffolding\Exception;

use Vegas\DI\Scaffolding\Exception;

/**
 * Class DeleteFailureException
 * @package Vegas\DI\Scaffolding\Exception
 */
class DeleteFailureException extends Exception
{
    /**
     * Exception default message
     */
    protected $message = 'Unable to delete record.';   
}
 