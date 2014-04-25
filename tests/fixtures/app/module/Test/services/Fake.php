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
 
namespace Test\Services;

use Phalcon\DI\InjectionAwareInterface;
use Vegas\DI\InjectionAwareTrait;

class Fake implements InjectionAwareInterface
{
    use InjectionAwareTrait;

} 