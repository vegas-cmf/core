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

namespace Vegas\Tests\Db\Decorator\Helper;

class SlugTestModel extends \Vegas\Db\Decorator\CollectionAbstract {}

class SlugTraitTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldGenerateSlug()
    {
        $test = new SlugTestModel();
        $test->generateSlug('Lorem ipsum');

        $this->assertEquals(\Phalcon\Utils\Slug::generate('Lorem ipsum'), $test->slug);
    }
}
 