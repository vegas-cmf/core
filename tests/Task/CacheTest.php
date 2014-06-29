<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Task;

use Vegas\Task\AssetsTask;
use Vegas\Tests\Cli\TestCase;

class CacheTest extends TestCase
{
    public function testValidCoreTask()
    {
        $this->bootstrap->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'vegas:cache',
            2 => 'clean'
        ));

        ob_start();

        $this->bootstrap->setup()->run();
        $result = ob_get_contents();

        ob_end_clean();

        $this->assertEquals("Cleaning cache..\nDone.", $result);
    }
}