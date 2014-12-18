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

namespace Vegas;

/**
 * Interface BootstrapInterface
 * @package Vegas
 */
interface BootstrapInterface
{
    /**
     * Executes all bootstrap initialization methods
     * This method can be overloaded to load own initialization method.
     * @return mixed
     */
    public function setup();

    /**
     * @return mixed
     */
    public function getApplication();

    /**
     * @return mixed
     */
    public function getDI();

    /**
     * Runs application
     *
     * @return mixed
     */
    public function run();
}
 