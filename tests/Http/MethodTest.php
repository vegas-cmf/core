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
 
namespace Vegas\Tests\Http;


use Vegas\Http\Method;

class MethodTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayListOfMethods()
    {
        $methods = Method::toArray();
        $this->assertInternalType('array', $methods);
        $this->assertSameSize(array('GET','POST','DELETE','OPTIONS','HEAD','PUT','PATCH'), $methods);
    }

} 