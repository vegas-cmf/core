<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Db\Decorator;


use Phalcon\Utils\Slug;
use Vegas\Db\HasMappingTrait;

abstract class ModelAbstract extends \Phalcon\Mvc\Model
{
    use HasMappingTrait;
    use MappingHelperTrait;

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
} 