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

class Action
{
    private $name;

    private $description;

    private $options = array();

    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function getName()
    {
        return $this->name;
    }

    public function addOption(Option $option)
    {
        $this->options[] = $option;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function validate($param, $value)
    {
        foreach ($this->options as $option) {
            if ($option->matchParam($param)) {
                $option->validate($value);
            }
        }

        return true;
    }
} 