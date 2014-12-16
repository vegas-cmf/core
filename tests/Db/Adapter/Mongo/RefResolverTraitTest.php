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
 

namespace Vegas\Tests\Db\Adapter\Mongo;


use Ref\Models\Child;
use Ref\Models\Parental;
use Vegas\Db\Adapter\Mongo\DbRef;

class RefResolverTraitTest extends \Vegas\Test\TestCase
{
    public function testShouldFindCorrespondingRecordFromMapping()
    {
        $parent = new Parental();
        $parent->name = 'Parent';
        $parent->save();

        $child = new Child();
        $child->name = 'Child';
        $child->parent = DbRef::create($parent);
        $child->save();

        $correspondingRecord = $child->readRef('parent');
        $this->assertInstanceOf('Ref\Models\Parental', $correspondingRecord);
        $this->assertEquals((string) $parent->getId(), (string) $correspondingRecord->getId());
    }

    public function testShouldFindCorrespondingRecordFromPropertyMap()
    {
        $grandParent = new Parental();
        $grandParent ->name = 'Grand parent';
        $grandParent->save();

        $child = new Child();
        $child->name = 'Child';
        $child->grandParent = DbRef::create($grandParent);
        $child->save();

        $correspondingRecord = $child->readRef('grandParent');
        $this->assertInstanceOf('Ref\Models\Parental', $correspondingRecord);
        $this->assertEquals((string) $grandParent->getId(), (string) $correspondingRecord->getId());
    }

    public function testShouldReturnOriginValueWhenNoResolved()
    {
        $parent = new Parental();
        $parent->name = 'Parent';
        $parent->save();

        $child = new Child();
        $child->name = 'Child';
        $child->parent = DbRef::create($parent);
        $child->save();

        $this->getDI()->remove('mongoMapper');
        $this->assertEquals(DbRef::create($parent)['$id'], $child->readRef('parent')['$id']);
        $this->assertEquals(DbRef::create($parent)['$ref'], $child->readRef('parent')['$ref']);
    }

    public function testShouldThrowExceptionForInvalidMongoDBRef()
    {
        $parent = new Parental();
        $parent->name = 'Parent';
        $parent->save();

        $child = new Child();
        $child->name = 'Child';
        $child->parent = DbRef::create($parent);
        $child->save();

        $exception = null;
        try {
            $child->readRef('name');
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Db\Adapter\Mongo\Exception\InvalidReferenceException', $exception);

        $exception = null;
        try {
            $child->readRef('fake.nested');
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Db\Adapter\Mongo\Exception\InvalidReferenceException', $exception);
    }

} 