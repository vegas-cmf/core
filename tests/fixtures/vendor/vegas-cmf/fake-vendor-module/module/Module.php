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

namespace FakeVendorModule;

class Module extends \Vegas\Mvc\ModuleAbstract
{
    public function __construct() {
        $this->namespace = __NAMESPACE__;
        $this->dir = __DIR__;
    }
} 