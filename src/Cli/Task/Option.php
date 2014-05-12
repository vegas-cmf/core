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

namespace Vegas\Cli\Task;

use Vegas\Cli\Task\Exception\InvalidArgumentException;

class Option
{
    protected $name;

    protected $shortName;

    /**
     * @var callable
     */
    protected $validator;

    protected $description;

    public function __construct($name, $shortName, $description = '')
    {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->description = $description;
    }

    public function setValidator(callable $validator)
    {
        $this->validator = $validator;
    }

    public function matchParam($paramName)
    {
        return (0 == strcasecmp($this->name, $paramName)) || (0 == strcasecmp($this->shortName, $paramName));
    }

    public function validate($value)
    {
        $result = true;
        if (is_callable($this->validator)) {
            $result = call_user_func($this->validator, $value);
            if (!$result) {
                throw new InvalidArgumentException();
            }
        }

        return $result;
    }
}
 