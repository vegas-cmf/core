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
namespace Vegas\Mvc\Dispatcher;

class Error
{
    private $code;
    private $message;
    
    /**
     * @param int $code
     * @param string $message
     */
    public function __construct($code, $message) {
        $this->code = $code;
        $this->message = $message;
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function getMessage()
    {
        return $this->message;
    }
}
