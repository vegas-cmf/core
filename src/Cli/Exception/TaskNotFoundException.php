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
 
namespace Vegas\Cli\Exception;

use Vegas\Cli\Exception as CliException;

/**
 * Class TaskNotFoundException
 * @package Vegas\Cli\Exception
 */
class TaskNotFoundException extends CliException
{
    /**
     * Exception default message
     *
     * @var string
     */
    protected $message = 'Task not found';
}
