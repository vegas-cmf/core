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

/**
 * Class Option
 * @package Vegas\Cli\Task
 */
class Option
{
    /**
     * Long name of option with `--` as prefix
     *
     * @var string
     */
    protected $name;

    /**
     * Short name of option with `-` as prefix
     *
     * @var string
     */
    protected $shortName;

    /**
     * Validation function
     *
     * @var callable
     */
    protected $validator;

    /**
     * Description of option
     *
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $isRequired;

    /**
     * @param $name
     * @param $shortName
     * @param string $description
     */
    public function __construct($name, $shortName, $description = '')
    {
        $this->name = $name;
        $this->shortName = $shortName;
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
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets validator function
     *
     * @param callable $validator
     */
    public function setValidator(callable $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Checks if parameter name matches to long or short name of option
     *
     * @param $paramName
     * @return bool
     */
    public function matchParam($paramName)
    {
        return (0 == strcasecmp($this->name, $paramName)) || (0 == strcasecmp($this->shortName, $paramName));
    }

    /**
     * Determines if option is required
     *
     * @param bool $required
     * @return $this
     */
    public function setRequired($required = false)
    {
        $this->isRequired = $required;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->isRequired;
    }

    /**
     * Validates option value
     *
     * @param $value
     * @return bool|mixed
     */
    public function validate($value)
    {
        $result = !$this->isRequired();
        if (is_callable($this->validator)) {
            $result = call_user_func($this->validator, $value);
        }

        return $result;
    }

    /**
     * @param $args
     * @return mixed
     */
    public function getValue($args)
    {
        if (isset($args[$this->name])) {
            return $args[$this->name];
        }

        if (isset($args[$this->shortName])) {
            return $args[$this->shortName];
        }
    }
}
 