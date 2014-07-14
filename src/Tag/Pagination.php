<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 *         Jaroslaw Macko <jarek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Tag;

/**
 * Class Pagination
 * @package Vegas\Tag
 */
class Pagination
{
    /**
     * Pagination link html code
     *
     * @var string
     */
    private $htmlHref = '<a href="%s?page=%d">%s</a>';

    /**
     * Pagination list element
     *
     * @var string
     */
    private $htmlElement = '<li class="%s">%s</li>';

    /**
     * Pagination current URI
     *
     * @var
     */
    private $currentUri;

    /**
     * Pagination settings
     *
     * @var array
     */
    private $settings;

    /**
     * @param \Phalcon\DI $di
     */
    public function __construct(\Phalcon\DI $di)
    {
        $this->di = $di;
        $this->settings = array(
            'start_end_offset' => 2,
            'middle_offset' => 3,
            'label_next' => 'Next',
            'label_previous' => 'Previous'
        );
    }

    /**
     * Renders pagination html
     *
     * @param $page
     * @param array $settings
     * @return string
     */
    public function render($page, $settings=array())
    {
        $this->settings = array_merge($this->settings, $settings);

        if (!empty($page->currentUri)) {
            $this->htmlHref = str_replace('?', '&', $this->htmlHref);
            $this->currentUri = $page->currentUri;
        } else {
            $this->currentUri = $this->di->get('router')->getRewriteUri();
        }

        $html = '';
        if ($page->total_pages > 1) {
            $this->page = $page;

            $this->checkBoundary();

            $html .= '<ul class="pagination">';
            $html .= $this->renderBefore();
            $html .= $this->renderPages();
            $html .= $this->renderNext();
            $html .= '</ul>';
        }

        return $html;
    }

    /**
     *
     */
    private function checkBoundary()
    {
        if($this->page->current > $this->page->total_pages) {

            $queryLink = !empty($this->page->currentUri) ? '&' : '?';
            $redirectUrl = substr($this->currentUri, 1) . $queryLink . 'page=' . $this->page->total_pages;

            $this->di->get('response')->redirect($redirectUrl);
        }
    }

    /**
     * @return string
     */
    private function renderBefore()
    {
        $before = $this->page->before;
        $extraClass = $before ? '' : ' not-active';
        if (empty($before)) {
            $before = 1;
        }
        return $this->renderElement($before, $this->settings['label_previous'], 'prev'.$extraClass);
    }

    /**
     * @return string
     */
    private function renderNext()
    {
        $next = $this->page->next;
        $extraClass = $next ? '' : ' not-active';
        if (empty($next)) {
            $next = $this->page->total_pages;
        }
        return $this->renderElement($next, $this->settings['label_next'], 'next'.$extraClass);
    }

    /**
     * @param $page
     * @param $title
     * @param string $class
     * @return string
     */
    private function renderElement($page, $title, $class = '')
    {
        $href = sprintf($this->htmlHref, $this->currentUri, $page, $title);
        return sprintf($this->htmlElement, $class, $href);
    }

    /**
     * @return string
     */
    private function renderPages()
    {
        $html = '';

        for($i=1; $i<=$this->page->total_pages; $i++) {
            if ($i == $this->page->current) {
                $html .= $this->renderElement($i, $i, 'active');
            } elseif($this->isPrintablePage($i)) {
                $html .= $this->renderElement($i, $i);
            } elseif($this->isMiddleOffsetPage($i)) {
                $html .= $this->renderElement($this->page->current, '...', 'more');
            }
        }

        return $html;
    }

    /**
     * @param $pageNb
     * @return bool
     */
    private function isPrintablePage($pageNb)
    {
        if (abs($this->page->current-$pageNb) <= $this->settings['middle_offset']) {
            return true;
        }

        if (($pageNb - $this->settings['start_end_offset']) <= 0) {
            return true;
        }

        if (($this->page->total_pages-$pageNb) < $this->settings['start_end_offset']) {
            return true;
        }

        return false;
    }

    /**
     * @param $pageNb
     * @return bool
     */
    private function isMiddleOffsetPage($pageNb)
    {
        return (abs($this->page->current - $pageNb) == ($this->settings['middle_offset']+1));
    }
}
