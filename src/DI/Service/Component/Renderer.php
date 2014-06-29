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

class Renderer implements RendererInterface
{
    protected $view;
    protected $moduleName;
    protected $templateName;
    
    public function __construct(\Phalcon\Mvc\View $view = null)
    {
        $this->view = $view;
    }
    
    public function setModuleName($name)
    {
        $this->moduleName = $name;
        
        return $this;
    }
    
    public function setTemplateName($name)
    {
        $this->templateName = $name;
        
        return $this;
    }
    
    public function render($params = array())
    {
        return $this->view->partial($this->getServiceViewPath(), $params);
    }
    
    private function getServiceViewPath()
    {
        return '../../'.$this->moduleName.'/views/services/'.$this->templateName;
    }
}