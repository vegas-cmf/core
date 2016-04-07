<?php
namespace Vegas\Tests\Task;

use Vegas\Tests\Cli\TestCase;

class I18nTest extends TestCase
{
    public function testPublishAction()
    {
        $result = $this->runCliAction('cli/cli.php vegas:i18n generate');

        $this->assertContains("Scanning", $result);
        $this->assertContains("Preparing final files...", $result);
        $this->assertContains("Generated files", $result);
    }
}