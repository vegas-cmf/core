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
 
namespace Vegas\Mvc\View\Engine;

use Vegas\Mvc\View\Engine\Volt\Exception\InvalidFilterException;
use Vegas\Mvc\View\Engine\Volt\Exception\UnknownFilterException;
use Vegas\Mvc\View\Engine\Volt\VoltFilterAbstract;
use Vegas\Mvc\View\Engine\Volt\VoltHelperAbstract;

/**
 * Class Volt
 * @package Vegas\Mvc\View\Engine
 */
class Volt extends \Phalcon\Mvc\View\Engine\Volt
{
    use RegisterFilters;
    use RegisterHelpers;

    /**
     * Registers a new filter in the compiler
     *
     * @param $filterName
     * @throws Volt\Exception\UnknownFilterException
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
        } catch (\Exception $e) {
            throw new UnknownFilterException(sprintf('Filter \'%s\' does not exist', $filterName));
        }
    }

    /**
     * Registers a new helper in the compiler
     *
     * @param $helperName
     * @throws Volt\Exception\UnknownFilterException
     */
    public function registerHelper($helperName)
    {
        $className = __CLASS__ . '\\Helper\\' . ucfirst($helperName);
        try {
            $helperInstance = $this->getClassInstance($className);
            if (!$helperInstance instanceof VoltHelperAbstract) {
                throw new InvalidFilterException();
            }
            $this->getCompiler()->addFunction($helperName, $helperInstance->getHelper());
        } catch (\Exception $e) {
            throw new UnknownFilterException(sprintf('Helper \'%s\' does not exist', $helperName));
        }
    }

    /**
     * Creates an instance of indicated class name
     *
     * @param $className
     * @return object
     */
    private function getClassInstance($className)
    {
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->newInstance($this->getCompiler());
    }
} 