<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\DI\Scaffolding\Adapter;

use Phalcon\DI;
use Vegas\DI\Scaffolding\Exception\RecordNotFoundException;

class Mongo implements \Vegas\Db\AdapterInterface, \Vegas\DI\Scaffolding\AdapterInterface
{
    use \Vegas\Db\Mongo\AdapterTrait;

    protected $scaffolding;

    public function __construct()
    {
        $di = DI::getDefault();
        $this->verifyRequiredServices($di);
        $this->setupExtraServices($di);
    }

    public function retrieveOne($id)
    {
        $record = call_user_func(array($this->scaffolding->getRecord(),'findById'),$id);
        
        if (!$record) {
            throw new RecordNotFoundException();
        }
        
        return $record;
    }
    
    public function setScaffolding(\Vegas\DI\Scaffolding $scaffolding) {
        $this->scaffolding = $scaffolding;
        
        return $this;
    }

}
