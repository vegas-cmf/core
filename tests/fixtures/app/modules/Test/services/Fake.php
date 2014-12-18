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
 
namespace Test\Services;

use Vegas\DI\InjectionAwareTrait;

class Fake extends \Vegas\DI\Service\ComponentAbstract
{
    public function setUp($params = array())
    {
        return array('test' => $params);
    }
} 