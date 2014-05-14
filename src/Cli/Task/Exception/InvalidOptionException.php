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
 * Class InvalidOptionException
 * @package Vegas\Cli\Task\Exception
 */
class InvalidOptionException extends CliException
{
    /**
     * @param string $option
     */
    public function __construct($option)
    {
        parent::__construct($option);
        $this->option = $option;
    }

    /**
     * @var string
     */
    protected $option;

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
    protected $message = 'Invalid option';
}
 