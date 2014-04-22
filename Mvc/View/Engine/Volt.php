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

class Volt extends \Phalcon\Mvc\View\Engine\Volt
{
    public function registerFilter($filterName)
    {
        $className = __NAMESPACE__ . '\\Filter\\' . ucfirst($filterName);
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

    public function registerHelper($helperName)
    {
        $className = __NAMESPACE__ . '\\Helper\\' . ucfirst($helperName);
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

    private function getClassInstance($className)
    {
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->newInstance();
    }
} 