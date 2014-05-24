<?php
/**
 * @author Sławomir Żytko <slawek@amsterdam-standard.pl>
 * @copyright (c) 2014, Amsterdam Standard
 */

namespace Vegas\Mvc\View\Engine;

/**
 * Class RegisterFilters
 * @package Vegas\Mvc\View\Engine
 */
trait RegisterFilters
{
    /**
     * @return string
     */
    private function getFiltersDirectoryPath()
    {
        $engineName = str_replace(__NAMESPACE__, '', __CLASS__);
        $path = __DIR__ . '/' . $engineName . '/Filter/';
        return $path;
    }

    /**
     *
     */
    public function registerFilters ()
    {
        foreach (glob($this->getFiltersDirectoryPath() . '*.php') as $file) {
            $filterName = pathinfo($file, PATHINFO_FILENAME);
            $this->registerFilter(lcfirst($filterName));
        }
    }
}
 