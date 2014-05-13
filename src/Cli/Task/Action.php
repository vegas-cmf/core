<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Cli\Task;

use Vegas\Cli\Task\Exception\InvalidArgumentException;
use Vegas\Cli\Task\Exception\InvalidOptionException;

/**
 * Class Action
 * @package Vegas\Cli\Task
 */
class Action
{
    /**
     * Name of action
     *
     * @var string
     */
    private $name;

    /**
     * Description of action
     *
     * @var string
     */
    private $description;

    /**
     * Available options
     *
     * @var array
     */
    private $options = array();

    /**
     * @param $name
     * @param $description
     */
    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Adds option for action
     *
     * @param Option $option
     * @return $this
     */
    public function addOption(Option $option)
    {
        $this->options[] = $option;
        return $this;
    }

    /**
     * Returns available options for action
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Validates current action options
     *
     * @param $param
     * @param $value
     * @return bool
     * @throws Exception\InvalidArgumentException
     * @throws Exception\InvalidOptionException
     */
    public function validate($param, $value)
    {
        $matched = false;
        foreach ($this->options as $option) {
            //checks if options matches specified parameter
            if ($option->matchParam($param)) {
                //validates option
                if (!$option->validate($value)) {
                    throw new InvalidArgumentException($param, $value);
                }
                $matched = true;
            }
        }
        if (!$matched) {
            throw new InvalidOptionException($param);
        }

        return true;
    }
} 