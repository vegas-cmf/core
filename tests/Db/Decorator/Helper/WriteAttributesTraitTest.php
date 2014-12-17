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

class AttrTestModel extends CollectionAbstract {}


class WriteAttributesTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldWriteArrayOfAttributes()
    {
        $attrs = [
            'test' => 1,
            'test2' => 2,
            'test3' => 3
        ];

        $test = new AttrTestModel();
        $test->writeAttributes($attrs);

        $this->assertEquals(1, $test->readAttribute('test'));
        $this->assertEquals(2, $test->readAttribute('test2'));
        $this->assertEquals(3, $test->readAttribute('test3'));
    }
}
 