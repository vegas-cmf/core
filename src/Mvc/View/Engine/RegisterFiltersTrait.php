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

/**
 * Class RegisterFiltersTrait
 * @package Vegas\Mvc\View\Engine
 */
trait RegisterFiltersTrait
{
    /**
     * Returns the path to view filter directory
     *
     * @return string
     * @internal
     */
    private function getFiltersDirectoryPath()
    {
        $engineName = str_replace(__NAMESPACE__, '', __CLASS__);
        $path = __DIR__ . '/' . $engineName . '/Filter/';
        return $path;
    }

    /**
     * Registers view filters from directory
     */
    public function registerFilters ()
    {
        foreach (glob($this->getFiltersDirectoryPath() . '*.php') as $file) {
            $filterName = pathinfo($file, PATHINFO_FILENAME);
            $this->registerFilter(lcfirst($filterName));
        }
    }

    /**
     * Registers filter indicated by its name
     *
     * @param $filterName
     * @return mixed
     */
    abstract public function registerFilter($filterName);
}
 