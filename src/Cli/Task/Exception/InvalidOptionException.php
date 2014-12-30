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
 * Class InvalidOptionException
 * @package Vegas\Cli\Task\Exception
 */
class InvalidOptionException extends CliException
{
    /**
     * Constructor
     *
     * @param string $option
     */
    public function __construct($option)
    {
        parent::__construct($option);
        $this->option = $option;
    }

    /**
     * Option that caused exception
     * @var string
     */
    protected $option;

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
    protected $message = 'Invalid option';
}
