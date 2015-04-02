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
 * Class MongoAbstract
 * @package Vegas\Paginator\Adapter\Mongo
 */
abstract class MongoAbstract implements AdapterInterface
{
    /**
     * @var
     * @internal
     */
    protected  $db;

    /**
     * @var string
     * @internal
     */
    protected $modelName;

    /**
     * @var
     * @internal
     */
    protected $model;

    /**
     * @var int
     * @internal
     */
    protected $totalPages;

    /**
     * @var array
     * @internal
     */
    protected $query = array();

    /**
     * @var int
     * @internal
     */
    protected $limit = 10;

    /**
     * @var int
     * @internal
     */
    protected $page = 1;

    /**
     * @var mixed
     * @internal
     */
    protected $sort;

    /**
     * Constructor
     * Sets config as class properties
     *
     * @param array $config
     */
    public function __construct(array $config)
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
     * @inheritdoc
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
