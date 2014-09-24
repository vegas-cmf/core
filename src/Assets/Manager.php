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
namespace Vegas\Assets;

/**
 * Class Manager
 *
 * Simple assets manager, that prevents assets duplicate
 *
 * @package Vegas\Assets
 */
class Manager extends \Phalcon\Assets\Manager
{
    /**
     * {@inheritdoc}
     */
    public function addCss($path, $local = true, $filter = null, $attributes = null)
    {
        foreach ($this->getCss()->getResources() As $resource) {
            if ($resource->getPath() === $path) {
                return $this;
            }
        }
        
        return parent::addCss($path, $local, $filter, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function addJs($path, $local = true, $filter = null, $attributes = null)
    {
        foreach ($this->getJs()->getResources() As $resource) {
            if ($resource->getPath() === $path) {
                return $this;
            }
        }
        
        return parent::addJs($path, $local, $filter, $attributes);
    }
}
