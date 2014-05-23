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
use Vegas\Cli\Task\Exception\MissingArgumentException;
use Vegas\Cli\Task\Exception\MissingRequiredArgumentException;

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
     * @param array $args
     * @throws Exception\MissingRequiredArgumentException
     * @throws Exception\InvalidArgumentException
     * @throws Exception\InvalidOptionException
     * @return bool
     */
    public function validate($args)
    {
        $matched = false;

        //validates arguments
        foreach ($args as $arg => $value) {
            //non-named option are skipped from validation
            if (is_numeric($arg)) continue;
            //validates action options
            foreach ($this->options as $option) {
                //checks if options matches specified parameter
                if ($option->matchParam($arg)) {
                    //validates option
                    if (!$option->validate($value)) {
                        throw new InvalidArgumentException($arg, $value);
                    }
                    $matched = true;
                }
            }
            if (!$matched) {
                throw new InvalidOptionException($arg);
            }
        }
        //validates required options
        foreach ($this->options as $option) {
            if (!$option->isRequired()) {
                continue;
            }

            if (!$option->getValue($args)) {
                throw new MissingRequiredArgumentException($option->getShortName());
            }
        }

        return true;
    }
} 