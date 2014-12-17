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

namespace Vegas\Fake\Task;

use Vegas\Cli\Task\Action;

class FakeTask extends \Vegas\Cli\TaskAbstract
{
    /**
     * Task must implement this method to set available options
     *
     * @return mixed
     */
    public function setupOptions()
    {
        $this->addTaskAction(new Action('test', 't', 'Test action'));
    }

    public function testAction()
    {
        $this->putText(__CLASS__);
    }
}