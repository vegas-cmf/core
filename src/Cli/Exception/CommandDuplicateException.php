<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Cli\Exception;

use Vegas\Cli\Exception as CliException;

class CommandDuplicateException extends CliException
{
    protected $message = 'Command with the same name has been already registered';
}
 