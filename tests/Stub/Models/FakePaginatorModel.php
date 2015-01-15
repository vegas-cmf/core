<?php
/**
 * This file is part of Vegas package
 *
 * @author Mateusz Aniolek <mateusz.aniolek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Stub\Models;

class FakePaginatorModel extends \Vegas\Db\Decorator\CollectionAbstract
{
    public function getSource()
    {
        return 'vegas_paginator';
    }
}
 