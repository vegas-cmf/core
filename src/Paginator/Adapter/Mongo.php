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
namespace Vegas\Paginator\Adapter;

use Phalcon\Paginator\AdapterInterface;

class Mongo implements AdapterInterface
{
    private $db;
    private $modelName;
    private $model;
    private $totalPages;
    
    private $query = array();
    private $limit = 10;
    private $page = 1;
    private $sort;
    
    public function __construct($config)
    {
        foreach ($config As $key => $value) {
            $this->$key = $value;
        }
        
        $this->validate();
        
        $this->model = new $this->modelName();
    }

    private function validate()
    {
        if (empty($this->modelName)) {
            throw new Exception\ModelNotSetException();
        }
        
        if (empty($this->db)) {
            throw new Exception\DbNotSetException();
        }
    }
    
    public function getPaginate()
    {
        $page = new \Vegas\Paginator\Page();
        
        $page->current = $this->page;
        $page->next = $this->getNextPage();
        $page->before = $this->getPreviousPage();
        $page->total_pages = $this->getTotalPages();
        $page->items = $this->getResults();
        
        return $page;
    }

    public function getPreviousPage()
    {
        if ($this->page > 1) {
            return ($this->page-1);
        }
        
        return null;
    }
    
    public function getNextPage()
    {
        if ($this->page < $this->getTotalPages()) {
            return ($this->page+1);
        }
        
        return null;
    }
    
    public function getTotalPages()
    {
        if (empty($this->totalPages)) {
            $this->totalPages = (int)ceil($this->getCursor()->count()/$this->limit);
        }
        
        return $this->totalPages;
    }
    
    public function setCurrentPage($page)
    {
        $this->page = $page;
        
        return $this;
    }
    
    public function getResults()
    {
        $skip = ($this->page-1)*$this->limit;
        
        $cursor = $this->getCursor();
        $cursor->skip($skip)->limit($this->limit);
        
        if (!empty($this->sort)) {
            $cursor->sort($this->sort);
        }
        
        $results = array();
        
        foreach ($cursor As $row) {
            $object = new $this->modelName();
            $object->writeAttributes($row);
            
            $results[] = $object;
        }
        
        return $results;
    }
    
    private function getCursor()
    {
        $source = $this->model->getSource();
        $cursor = $this->db->$source->find($this->query);
        
        return $cursor;
    }
}
