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

namespace Vegas\Tests\Db\Decorator\Helper;

use Vegas\Db\Decorator\CollectionAbstract;

class NestedAttrTestModel extends CollectionAbstract {}


class ReadNestedAttributeTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldReadNestedAttributesFromArray()
    {
        $test = new NestedAttrTestModel();
        $test->a = [
            'b' => [
                'c' => 'test'
            ]
        ];

        $this->assertInternalType('array', $test->a);
        $this->assertEquals('test', $test->readNestedAttribute('a.b.c'));
        $this->assertSame(['c' => 'test'], $test->readNestedAttribute('a.b'));
        $this->assertSame(['b' => ['c' => 'test']], $test->readNestedAttribute('a'));
    }

    public function testShouldReadNestedAttributesFromObject()
    {
        $test = new NestedAttrTestModel();

        $a = new \stdClass();
        $a->b = new \stdClass();
        $a->b->c = 'test';
        $test->a = $a;

        $this->assertInternalType('object', $test->a);
        $this->assertEquals('test', $test->readNestedAttribute('a.b.c'));
        $this->assertSame($a->b, $test->readNestedAttribute('a.b'));
        $this->assertSame($a, $test->readNestedAttribute('a'));
    }

    public function testShouldReturnNullForInvalidNestedAttrName()
    {
        $test = new NestedAttrTestModel();

        $a = new \stdClass();
        $a->b = new \stdClass();
        $a->b->c = 'test';
        $test->a = $a;
        $this->assertNull($test->readNestedAttribute('a.b.c.d'));

        $test->a = [
            'b' => [
                'c' => 'test'
            ]
        ];
        $this->assertNull($test->readNestedAttribute('a.b.c.d'));

        $this->assertNull($test->readNestedAttribute(''));
        $this->assertNull($test->readNestedAttribute(false));
        $this->assertNull($test->readNestedAttribute(0));
        $this->assertNull($test->readNestedAttribute(1));
    }
}
 