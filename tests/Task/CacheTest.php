<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Task;

use Vegas\Tests\Cli\TestCase;

class CacheTest extends TestCase
{
    public function testValidCoreTask()
    {
        $result = $this->runCliAction('cli/cli.php vegas:cache clean');

        $this->assertContains("Cleaning cache", $result);
    }
}
