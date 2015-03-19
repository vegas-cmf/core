<?php
/**
 * This file is part of Vegas package
 *
 * @author Mateusz Aniolek <mateusz.aniolek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Paginator\Adapter;

use Vegas\Paginator\Adapter\Mongo\AggregateCursor;

/**
 * Class AggregateMongo
 * @package Vegas\Paginator\Adapter
 */
class AggregateMongo extends MongoAbstract
{
    /**
     * @inheritdoc
     */
    public function __construct($config)
    {
        if (isset($config['aggregate'])) {
            $config['query'] = $config['aggregate'];
        }

        parent::__construct($config);
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
        $cursor->skip($skip);

        $results = array();
        $i = 0;

        while($cursor->valid() && $i++ < $this->limit) {

            $object = new $this->modelName();
            $object->writeAttributes($cursor->current());

            $pseudoCursor = new \stdClass();
            foreach ($object as $key => $value) {
                $pseudoCursor->$key = $value;
            }

            $results[] = $pseudoCursor;
            $cursor->skip();
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
        $cursor = new AggregateCursor($this->db, $source, $this->query);

        return $cursor;
    }

}
