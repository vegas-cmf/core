<?php
/**
 * This file is part of Vegas package
 *
 * @author Radosław Fąfara <radek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Task;

use Vegas\Tests\Cli\TestCase;

class ModuleTest extends TestCase
{
    public function testDumpAction()
    {
        unlink(TESTS_ROOT_DIR.'/fixtures/app/config/modules.php');
        unlink(TESTS_ROOT_DIR.'/fixtures/app/config/services.php');

        $result = $this->runCliAction('cli/cli.php vegas:module dump');

        $this->assertFileExists(TESTS_ROOT_DIR.'/fixtures/app/config/modules.php');
        $this->assertFileExists(TESTS_ROOT_DIR.'/fixtures/app/config/services.php');

        $this->assertContains("Done.", $result);
    }
}
