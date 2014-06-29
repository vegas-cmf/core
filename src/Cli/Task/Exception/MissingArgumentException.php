<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
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
class MissingArgumentException extends CliException
{
    /**
     * @param string $argument
     */
    public function __construct($argument)
    {
        $this->message = strtr('Missing argument `:argument`', array(':argument' => $argument));
    }

    /**
     * @var string
     */
    protected $message = 'Missing argument';
}
 