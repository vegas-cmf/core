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

namespace Vegas\Cli\Task\Exception;

use Vegas\Cli\Exception as CliException;

/**
 * Class MissingArgumentException
 * @package Vegas\Cli\Task\Exception
 */
class MissingRequiredArgumentException extends CliException
{
    /**
     * Constructor
     *
     * @param string $argument
     */
    public function __construct($argument)
    {
        $this->message = strtr('Argument `:argument` is required', array(':argument' => $argument));
    }

    /**
     * Exception default message
     *
     * @var string
     */
    protected $message = 'Missing required argument';
}
