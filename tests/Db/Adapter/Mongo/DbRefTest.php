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

namespace Vegas\Tests\Db\Adapter\Mongo;

use Ref\Models\Child;
use Ref\Models\Parental;
use Vegas\Db\Adapter\Mongo\DbRef;

class DbRefTest extends \Vegas\Test\TestCase
{
    public function tearDown()
    {
        parent::setUp();
        foreach (Parental::find() as $p) {
            $p->delete();
        }
        foreach (Child::find() as $c) {
            $c->delete();
        }
    }

    public function testShouldCreateMongoDBRefFromEntity()
    {
        $parent = new Parental();
        $parent->name = 'Parent';
        $parent->save();

        $child = new Child();
        $child->name = 'Child';
        $child->parent = DbRef::create($parent);
        $child->save();

        $this->assertEquals((string)$child->readRef('parent')->getId(), (string)$parent->getId());
    }

    public function testShouldCreateMongoDBRefFromCollectionAndEntity()
    {
        $parent = new Parental();
        $parent->name = 'Parent';
        $parent->save();

        $child = new Child();
        $child->name = 'Child';
        $child->parent = DbRef::create('Parental', $parent);
        $child->save();

        $this->assertEquals((string)$child->readRef('parent')->getId(), (string)$parent->getId());
    }

    public function testShouldCreateMongoDBRefFromCollectionAndMongoId()
    {
        $parent = new Parental();
        $parent->name = 'Parent';
        $parent->save();

        $child = new Child();
        $child->name = 'Child';
        $child->parent = DbRef::create('Parental', $parent->getId());
        $child->save();

        $this->assertEquals((string)$child->readRef('parent')->getId(), (string)$parent->getId());
    }

    public function testShouldCreateMongoDBRefFromCollectionAndStringId()
    {
        $parent = new Parental();
        $parent->name = 'Parent';
        $parent->save();

        $child = new Child();
        $child->name = 'Child';
        $child->parent = DbRef::create('Parental', (string)$parent->getId());
        $child->save();

        $this->assertEquals((string)$child->readRef('parent')->getId(), (string)$parent->getId());
    }

    public function testShouldThrowExceptionForInvalidId()
    {
        $exception = null;
        try {
            $child = new Child();
            $child->name = 'Child';
            $child->parent = DbRef::create('Parental', 'fake');
            $child->save();
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\MongoException', $exception);
    }
} 