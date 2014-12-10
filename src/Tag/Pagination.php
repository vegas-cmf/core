<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
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
     * @internal
     */
    private $htmlAHref = '<a href="%s">%s</a>';

    /**
     * Pagination link url
     *
     * @var string
     * @internal
     */
    private $htmlHref = "%s?page=%d";

    /**
     * Pagination list element
     *
     * @var string
     * @internal
     */
    private $htmlElement = '<li class="%s">%s</li>';

    /**
     * Pagination current URI
     *
     * @var string
     * @internal
     */
    private $currentUri;

    /**
     * Pagination settings
     *
     * @var array
     * @internal
     */
    private $settings;

    /**
     * Constructor
     * Sets default settings
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
            $this->htmlAHref = str_replace('?', '&', $this->htmlAHref);
            $this->currentUri = $page->currentUri;
        } else {
            $this->currentUri = $this->di->get('router')->getRewriteUri();
        }

        $html = '';
        if ($page->total_pages > 1) {
            $this->page = $page;

            $this->checkBoundary();

            $html .= '<ul class="pagination">';
            $html .= $this->renderPreviousButton();
            $html .= $this->renderPages();
            $html .= $this->renderNextButton();
            $html .= '</ul>';
        }

        return $html;
    }

    /**
     * Checks if current page fit in total pages
     * @internal
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
     * Renders button - previous page
     *
     * @return string
     * @internal
     */
    private function renderPreviousButton()
    {
        $before = $this->page->before;
        $extraClass = $before ? '' : ' not-active';
        if (empty($before)) {
            $before = 1;
        }
        return $this->renderElement($before, $this->settings['label_previous'], 'prev'.$extraClass);
    }

    /**
     * Renders button - next page
     *
     * @return string
     * @internal
     */
    private function renderNextButton()
    {
        $next = $this->page->next;
        $extraClass = $next ? '' : ' not-active';
        if (empty($next)) {
            $next = $this->page->total_pages;
        }
        return $this->renderElement($next, $this->settings['label_next'], 'next'.$extraClass);
    }

    /**
     * Renders html element
     *
     * @param $page
     * @param $title
     * @param string $class
     * @return string
     * @internal
     */
    private function renderElement($page, $title, $class = '')
    {
        $href = sprintf($this->htmlHref, $this->currentUri, $page);
        $args = $this->getSortingArguments();
        if($args !== false){
            foreach($args as $key => $arg) {
                $href .= '&' . $key . '=' . $arg;
            }

        }
        $element = sprintf($this->htmlAHref, $href, $title);
        return sprintf($this->htmlElement, $class, $element);
    }

    private function getSortingArguments()
    {
        if(!isset($this->settings['sorting'])) {
            return false;
        }
        return $this->settings['sorting'];
    }

    /**
     * Renders the pages list
     *
     * @return string
     * @internal
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
     * @internal
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
     * @internal
     */
    private function isMiddleOffsetPage($pageNb)
    {
        return (abs($this->page->current - $pageNb) == ($this->settings['middle_offset']+1));
    }
}
