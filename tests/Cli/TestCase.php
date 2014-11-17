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
namespace Vegas\Tests\Cli;

use Phalcon\DI;
use Vegas\Cli\Bootstrap;
use Vegas\Mvc\Controller\Crud;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $bootstrap;
    protected $di;

    public function setUp()
    {
        $this->di = DI::getDefault();

        $bootstrap = new Bootstrap($this->di->get('config'));

        $this->bootstrap = $bootstrap;
    }

    /**
     * Shorthand for more descriptive CLI command testing
     * @param string $command full command string to be called
     * @return string
     */
    protected function runCliAction($command)
    {
        $this->bootstrap->setArguments(explode(' ', $command));

        ob_start();

        $this->bootstrap->setup()->run();
        $result = ob_get_contents();

        ob_end_clean();

        return $result;
    }
}
