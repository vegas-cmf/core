<?php
/**
 * This file is part of Vegas package
 *
 * @author Radosław Fąfara <radek@amsterdamstandard.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Mvc\View\Engine\Volt\Helper;

use Vegas\Mvc\View\Engine\Volt\VoltHelperAbstract;

/**
 * Class MethodExists
 * @package Vegas\Mvc\View\Engine\Volt\Helper
 */
class MethodExists extends VoltHelperAbstract
{

    /**
     * Proxies to method_exists() PHP function
     *
     * @return string
     */
    public function getHelper()
    {
        return 'method_exists';
    }
}
