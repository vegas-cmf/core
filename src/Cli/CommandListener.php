<?php
use Vegas\Cli\Command\Option;
use Vegas\Cli\CommandInterface;
use Vegas\Cli\Exception\CommandDuplicateException;

/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */ 

class CommandListener
{
    private $commands = array();

    public function addCommand(CommandInterface $command)
    {
        if (array_key_exists($command->getName(), $this->commands)) {
            throw new CommandDuplicateException();
        }

        $this->commands[] = $command;

        return $this;
    }

    public function handle($commandLine)
    {

    }
}
 