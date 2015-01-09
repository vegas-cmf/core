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

/**
 * Class Mongo
 * @package Vegas\Paginator\Adapter
 */
class Mongo extends MongoAbstract
{
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
    public function getCursor()
    {
        $source = $this->model->getSource();
        $cursor = $this->db->$source->find($this->query);

        return $cursor;
    }

}
