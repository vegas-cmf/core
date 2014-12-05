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

namespace Vegas\Tests\Util;

use Vegas\Util\FileWriter;

class FileWriterTest extends \Vegas\Tests\App\TestCase
{
    public function testShouldWriteContentToFile()
    {
        $content = sha1(time());
        $path = TESTS_ROOT_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'test.tmp';
        $this->assertGreaterThan(0, FileWriter::write($path, $content, false));
        $this->assertEquals($content, file_get_contents($path));

        unlink($path);
    }

    public function testShouldNotWriteContentToIdenticalFile()
    {
        $content = sha1(time());
        $path = TESTS_ROOT_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'test.tmp';
        $this->assertGreaterThan(0, FileWriter::write($path, $content, false));
        $this->assertSame(0, FileWriter::write($path, $content, true));

        unlink($path);
    }

    public function testShouldUpdateFileContentWithDifferentContent()
    {
        $content = sha1(microtime());
        $path = TESTS_ROOT_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'test.tmp';
        $this->assertGreaterThan(0, FileWriter::write($path, $content, false));
        $content = sha1(microtime());
        $this->assertGreaterThan(0, FileWriter::write($path, $content, true));

        unlink($path);
    }

    public function testShouldWriteContentToFileWhenNotExists()
    {
        $content = sha1(microtime());
        $path = TESTS_ROOT_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'test.tmp';
        $this->assertGreaterThan(0, FileWriter::write($path, $content, true));

        unlink($path);
    }
}
 