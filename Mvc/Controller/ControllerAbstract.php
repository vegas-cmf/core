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
namespace Vegas\Mvc\Controller;

use Phalcon\Mvc\Controller;

abstract class ControllerAbstract extends Controller
{
    public function initialize()
    {
    }

    protected function jsonResponse($data) 
    {
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');
        return $this->response->setJsonContent($data);
    }

    /**
     * _
     * Alias for $this->i18n->_().
     * Returns translated text.
     * 
     * @param mixed $text 
     * @access protected
     * @return string
     */
    protected function _($text)
    {
        return $this->i18n->_($text);
    }

    protected function throw403($message = '')
    {
        throw new \Vegas\Exception($message, 403);
    }
    
    protected function throw404($message = '')
    {
        throw new \Vegas\Exception($message, 404);
    }
    
    protected function throw500($message = '')
    {
        throw new \Vegas\Exception($message, 500);
    }
}
