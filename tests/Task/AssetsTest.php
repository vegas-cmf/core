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

class AssetsTest extends TestCase
{
    public function testPublishAction()
    {
        $this->bootstrap->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'vegas:assets',
            2 => 'publish'
        ));

        ob_start();

        $this->bootstrap->setup()->run();
        $result = ob_get_contents();

        ob_clean();

        unlink(TESTS_ROOT_DIR.'/fixtures/public/assets/css/another.css');
        unlink(TESTS_ROOT_DIR.'/fixtures/public/assets/css/test.css');

        $this->assertContains("example.js. File already exists", $result);
    }
}