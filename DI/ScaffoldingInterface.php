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
namespace Vegas\DI;

interface ScaffoldingInterface
{
    public function __construct(\Vegas\DI\Scaffolding\AdapterInterface $adapter);
    
    public function getAdapter();
    public function getRecord();
    public function getForm($entity = null);
    
    public function setFormName($name);
    public function setModelName($name); 
    
    public function doCreate(array $values);
    public function doUpdate($id, array $values);
    public function doDelete($id);
}
