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
     * Number of total pages
     *
     * @var int
     */
    public $total_pages;

    /**
     * Number of previous page
     *
     * @var int
     */
    public $before;

    /**
     * Number of current page
     *
     * @var int
     */
    public $current;

    /**
     * Number of next page
     *
     * @var int
     */
    public $next;

    /**
     * Pagination items
     *
     * @var array()|\Iterator
     */
    public $items;
}
