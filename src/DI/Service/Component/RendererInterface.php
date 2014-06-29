<?php
/**
 * This file is part of Vegas package
 * 
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\DI\Service\Component;

interface RendererInterface
{
    public function __construct(\Phalcon\Mvc\View $view = null);
    public function setModuleName($name);
    public function setTemplateName($name);
    public function render($params = array());
}