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
namespace Vegas\Paginator\Adapter;

use Phalcon\Paginator\AdapterInterface;

/**
 * Class Mongo
 * @package Vegas\Paginator\Adapter
 */
class Mongo implements AdapterInterface
{
    /**
     * @var
     * @internal
     */
    private $db;

    /**
     * @var string
     * @internal
     */
    private $modelName;

    /**
     * @var
     * @internal
     */
    private $model;

    /**
     * @var int
     * @internal
     */
    private $totalPages;

    /**
     * @var array
     * @internal
     */
    private $query = array();

    /**
     * @var int
     * @internal
     */
    private $limit = 10;

    /**
     * @var int
     * @internal
     */
    private $page = 1;

    /**
     * @var mixed
     * @internal
     */
    private $sort;

    /**
     * Constructor
     * Sets config as class properties
     *
     * @param $config
     */
    public function __construct($config)
    {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }

        $this->validate();
    }

    /**
     * Validates model and database
     *
     * @throws Exception\ModelNotSetException
     * @throws Exception\DbNotSetException
     * @internal
     */
    private function validate()
    {
        if (empty($this->modelName) && empty($this->model)) {
            throw new Exception\ModelNotSetException();
        }

        if (empty($this->model)) {
            $this->model = new $this->modelName();
        }

        if (empty($this->modelName)) {
            $this->modelName = get_class($this->model);
        }

        if (empty($this->db)) {
            $this->db = $this->model->getConnection();
        }
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * Returns previous page number
     *
     * @return int|null
     */
    public function getPreviousPage()
    {
        if ($this->page > 1) {
            return ($this->page-1);
        }

        return null;
    }

    /**
     * Returns next page number
     *
     * @return int|null
     */
    public function getNextPage()
    {
        if ($this->page < $this->getTotalPages()) {
            return ($this->page+1);
        }

        return null;
    }

    /**
     * Returns number of pages
     *
     * @return int
     */
    public function getTotalPages()
    {
        if (empty($this->totalPages)) {
            $this->totalPages = (int)ceil($this->getCursor()->count()/$this->limit);
        }

        return $this->totalPages;
    }

    /**
     * Sets current page
     *
     * @param int $page
     * @return $this
     */
    public function setCurrentPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Returns results for current page
     *
     * @return array
     */
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

    /**
     * @return mixed
     * @internal
     */
    private function getCursor()
    {
        $source = $this->model->getSource();
        $cursor = $this->db->$source->find($this->query);

        return $cursor;
    }
}
