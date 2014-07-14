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
namespace Vegas\Paginator;

/**
 * Class Page
 *
 * Represents page from paginator
 *
 * @package Vegas\Paginator
 */
class Page
{
    /**
     * @var
     */
    public $total_pages;

    /**
     * @var
     */
    public $before;

    /**
     * @var
     */
    public $current;

    /**
     * @var
     */
    public $next;

    /**
     * @var
     */
    public $items;
}
