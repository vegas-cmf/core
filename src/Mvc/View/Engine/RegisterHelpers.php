<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @internal
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
 