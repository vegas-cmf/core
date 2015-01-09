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
     * Determines if option is required
     *
     * @var bool
     */
    protected $isRequired = false;

    /**
     * Constructor
     * 
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
     * Returns full name of option
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns short name of option
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Return description of option
     *
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
        return (0 == strcasecmp($this->name, $paramName))
                || (0 == strcasecmp($this->shortName, $paramName));
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
     * Determines if option is required
     *
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
        $result = !$this->isRequired;
        if ($this->isRequired()) {
            $result = !empty($value);
        }
        if (is_callable($this->validator)) {
            $result = call_user_func($this->validator, $value);
        }

        return $result;
    }

    /**
     * Looks for value of option in specified arguments array.
     * When option is not found then returns default value.
     *
     * @param $args
     * @param null $default
     * @return mixed
     */
    public function getValue($args, $default = null)
    {
        if (isset($args[$this->name])) {
            return $args[$this->name];
        }

        if (isset($args[$this->shortName])) {
            return $args[$this->shortName];
        }

        return $default;
    }
}
