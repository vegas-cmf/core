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

namespace Vegas\Cli\Task\Exception;

use Vegas\Cli\Exception as CliException;

/**
 * Class InvalidArgumentException
 * @package Vegas\Cli\Task\Exception
 */
class InvalidArgumentException extends CliException
{
    /**
     * @param string $option
     * @param int $argument
     */
    public function __construct($option, $argument)
    {
        parent::__construct(sprintf('%s %s', $option, $argument));
        $this->option = $option;
        $this->argument = $argument;
    }

    /**
     * @var string
     */
    protected $option;

    /**
     * @var int
     */
    protected $argument;

    /**
     * @return int
     */
    public function getArgument()
    {
        return $this->argument;
    }

    /**
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @var string
     */
    protected $message = 'Invalid argument';
}
 