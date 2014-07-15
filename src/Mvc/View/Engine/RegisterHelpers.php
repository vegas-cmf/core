<?php
/**
 * @author Sławomir Żytko <slawek@amsterdam-standard.pl>
 * @copyright (c) 2014, Amsterdam Standard
 */

namespace Vegas\Mvc\View\Engine;

/**
 * Class RegisterHelpers
 * @package Vegas\Mvc\View\Engine
 */
trait RegisterHelpers
{
    /**
     * Returns the ath to view helpers directory
     *
     * @return string
     */
    private function getHelpersDirectoryPath()
    {
        $engineName = str_replace(__NAMESPACE__, '', __CLASS__);
        $path = __DIR__ . '/' . $engineName . '/Helper/';
        return $path;
    }

    /**
     * Registers view helpers
     */
    public function registerHelpers()
    {
        foreach (glob($this->getHelpersDirectoryPath() . '*.php') as $file) {
            $helperName = pathinfo($file, PATHINFO_FILENAME);
            $this->registerHelper(lcfirst($helperName));
        }
    }
}
 