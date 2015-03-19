<?php
/**
 * This file is part of Vegas package
 *
 * @author Mateusz Aniołek <mateusz.aniolek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Paginator\Adapter\Mongo;

/**
 * Class AggregateCursor
 * @package Vegas\Paginator\Adapter\Mongo
 */
class AggregateCursor
{
    /**
     * Database connection
     *
     * @var
     */
    private $db;

    /**
     * Stores model name
     *
     * @var string
     */
    private $model;


    /**
     * Mongo Aggregated Cursor
     *
     * @var string
     */
    private $cursor;

    /**
     * Default constructor
     *
     * @param $db
     * @param $model
     * @param $aggregateQuery
     * @param array $options
     */
    public function __construct($db, $model, $aggregateQuery, $options = array())
    {
        $this->db = $db;
        $this->model = $model;
        $this->cursor = $this->db->{$this->model}->aggregateCursor($aggregateQuery, $options);
        $this->cursor->rewind();
    }

    /**
     * Method for moving cursor by specified amount of record
     *
     * @param int $by
     * @return $this
     */
    public function skip($by = 1)
    {
        $i = 0;
        while($this->cursor->valid() && $i < $by) {
            $this->cursor->next();
            $i++;
        }
        return $this;
    }

    /**
     * Method for total records count in collections
     *
     * @return int
     */
    public function count()
    {
        return $this->db->{$this->model}->count();
    }

    /**
     * Gets current record under the cursor
     *
     * @return mixed
     */
    public function current()
    {
        return isset($this->cursor->current()['current']) ? $this->cursor->current()['current'] : null;
    }

    /**
     * Checks if next element exists
     *
     * @return mixed
     */
    public function valid()
    {
        return $this->cursor->valid();
    }

}

