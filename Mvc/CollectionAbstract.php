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
namespace Vegas\Mvc;

use Phalcon\Utils\Slug;

abstract class CollectionAbstract extends \Phalcon\Mvc\Collection
{
    private $fileObjects;
    private $latestMapedFileValues = null;
    
    public function beforeCreate()
    {
        $this->created_at = new \MongoInt32(time());
    }

    public function beforeUpdate()
    {
        $this->updated_at = new \MongoInt32(time());
    }

    public function generateSlug($string)
    {
        $slug = new Slug();
        $this->slug = $slug->generate($string);
    }
    
    public function writeAttributes($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->writeAttribute($attribute, $value);
        }
    }
    
    public function readAttribute($name)
    {
        $values = parent::readAttribute($name);
        
        if ($name === 'files' && !empty($values)) {
            return $this->getFilesFor($values);
        }
        
        return $values;
    }
    
    public function getFiles()
    {
        return $this->readAttribute('files');
    }
    
    private function getFilesFor($values)
    {
        if ($this->latestMapedFileValues !== $values || !is_array($this->fileObjects)) {
            return $this->mapFiles($values);
        }
        
        return $this->fileObjects;
    }
    
    /**
     * @return array 
     * @throws NoFilesToMapException
     */
    private function mapFiles($values)
    {
        $this->fileObjects = array();
        $this->latestMapedFileValues = $values;
        
        if ($this->getDI()->has('fileWrapper') && $this->readAttribute('files') !== null) {
            $this->fileObjects = $this->getDI()->get('fileWrapper')->wrapValues($values);
        }
        
        return $this->fileObjects;
    }
    
    protected function afterDelete()
    {
        if (!empty($this->files)) {
            foreach ($this->getFiles() As $file) {
                $file->delete();
            }
        }
    }
}
