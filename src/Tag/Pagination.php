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
        $this->currentUri = $this->di->get('router')->getRewriteUri();

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
     */
    private function checkBoundary()
    {
        if($this->page->current > $this->page->total_pages) {
            $this->di->get('response')->redirect(substr($this->currentUri,1).'?page='.$this->page->total_pages);
        }
    }

    /**
     * Renders button - previous page
     *
     * @return string
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
     */
    private function renderElement($page, $title, $class = '')
    {
        $href = sprintf($this->htmlHref, $this->currentUri, $page, $title);
        return sprintf($this->htmlElement, $class, $href);
    }

    /**
     * Renders the pages list
     *
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
