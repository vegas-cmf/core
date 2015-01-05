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

namespace Vegas\Util;

use Vegas\Exception;

class DateTime extends \DateTime implements \JsonSerializable
{
    /**
     * Default format used for the whole class
     * @var string
     */
    public static $globalDefaultFormat = 'Y-m-d H:i:s';

    /**
     * Default format used for this object
     * @var string
     */
    private $defaultFormat;

    /**
     * @param string $time
     * @param \DateTimeZone $timezone
     * @throws Exception
     */
    public function __construct($time = 'now', \DateTimeZone $timezone = null)
    {
        if (is_numeric($time)) {
            throw new Exception('Invalid date format provided for DateTime: missing `@` prefix.');
        }
        return parent::__construct($time, $timezone);
    }

    /**
     * Prints the object as string using default format available.
     * @return string
     */
    public function __toString()
    {
        if (is_string($this->defaultFormat)) {
            return $this->format($this->defaultFormat);
        } else if (is_string(self::$globalDefaultFormat)) {
            return $this->format(self::$globalDefaultFormat);
        }
        return '';
    }

    /**
     * Sets default format
     *
     * @param string $defaultFormat
     * @return $this
     */
    public function setDefaultFormat($defaultFormat)
    {
        $this->defaultFormat = $defaultFormat;
        return $this;
    }

    /**
     * Default \DateTime method creates parent classtype object.
     * This implementation overrides this behavior.
     * {@inheritdoc}
     */
    public static function createFromFormat($format, $time, $object = NULL)
    {
        $date = $object ? \DateTime::createFromFormat($format, $time, $object)
            : \DateTime::createFromFormat($format, $time);

        return $date !== false ?
            new self($date->format('Y-m-d H:i:s'), $date->getTimezone())
            : false;
    }

    /**
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return $this->format(self::ISO8601);
    }

    /**
     * Validates date
     *
     * @param mixed $value
     * @return bool
     */
    public static function isValid($value)
    {
        if ($value instanceof \DateTime)
            return true;
        try {
            new static($value);
            return !is_null($value);
        } catch (\Exception $e) {
            return false;
        }
    }
}
