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
namespace Vegas\DI\Service;

interface ComponentInterface
{
    public function __construct(Component\RendererInterface $renderer = null);
    public function setRenderer(Component\RendererInterface $renderer);
    public function getRenderer();
    public function render($params = array());
}