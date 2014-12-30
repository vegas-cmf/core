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
 * Class InvalidArgumentException
 * @package Vegas\Cli\Task\Exception
 */
class InvalidArgumentException extends CliException
{
    /**
     * Constructor
     * 
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
     * Option that caused exception
     *
     * @var string
     */
    protected $option;

    /**
     * Argument that caused exception
     *
     * @var int
     */
    protected $argument;

    /**
     * Returns argument that caused exception
     *
     * @return int
     */
    public function getArgument()
    {
        return $this->argument;
    }

    /**
     * Returns option that caused exception
     *
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Exception default message
     *
     * @var string
     */
    protected $message = 'Invalid argument';
}
