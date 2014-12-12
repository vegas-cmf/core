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

namespace Vegas\Mvc\View\Engine;

use Vegas\Mvc\View\Engine\Volt\Exception\InvalidFilterException;
use Vegas\Mvc\View\Engine\Volt\Exception\InvalidHelperException;
use Vegas\Mvc\View\Engine\Volt\Exception\UnknownFilterException;
use Vegas\Mvc\View\Engine\Volt\Exception\UnknownHelperException;
use Vegas\Mvc\View\Engine\Volt\VoltFilterAbstract;
use Vegas\Mvc\View\Engine\Volt\VoltHelperAbstract;

/**
 * Class Volt
 * @package Vegas\Mvc\View\Engine
 */
class Volt extends \Phalcon\Mvc\View\Engine\Volt
{
    use RegisterFiltersTrait;
    use RegisterHelpersTrait;

    /**
     * Extension of template file
     *
     * @var string
     */
    private $extension = '.volt';

    /**
     * Sets template file extension
     *
     * @param $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * Registers a new filter in the compiler
     *
     * @param $filterName
     * @throws InvalidFilterException
     * @throws UnknownFilterException
     */
    public function registerFilter($filterName)
    {
        $className = __CLASS__ . '\\Filter\\' . ucfirst($filterName);
        try {
            $filterInstance = $this->getClassInstance($className);
            if (!$filterInstance instanceof VoltFilterAbstract) {
                throw new InvalidFilterException();
            }
            $this->getCompiler()->addFilter($filterName, $filterInstance->getFilter());
        } catch (\ReflectionException $e) {
            throw new UnknownFilterException(sprintf('Filter \'%s\' does not exist', $filterName));
        } catch (\Exception $e) {
            throw new InvalidFilterException(sprintf('Invalid filter \'%s\'', $filterName));
        }
    }

    /**
     * Registers a new helper in the compiler
     *
     * @param $helperName
     * @throws InvalidHelperException
     * @throws UnknownHelperException
     */
    public function registerHelper($helperName)
    {
        $className = __CLASS__ . '\\Helper\\' . ucfirst($helperName);
        try {
            $helperInstance = $this->getClassInstance($className);
            if (!$helperInstance instanceof VoltHelperAbstract) {
                throw new InvalidHelperException();
            }
            $this->getCompiler()->addFunction($helperName, $helperInstance->getHelper());
        } catch (\ReflectionException $e) {
            throw new UnknownHelperException(sprintf('Helper \'%s\' does not exist', $helperName));
        } catch (\Exception $e) {
            throw new InvalidHelperException(sprintf('Invalid helper \'%s\'', $helperName));
        }
    }

    /**
     * Creates an instance of indicated class name
     *
     * @param $className
     * @return object
     * @internal
     */
    private function getClassInstance($className)
    {
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->newInstance($this->getCompiler());
    }
}