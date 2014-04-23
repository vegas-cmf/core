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

/**
 * Class Error
 * @package Vegas\Mvc\Dispatcher
 */
class Error
{
    /**
     * Error code
     *
     * @var int
     */
    private $code;

    /**
     * Error message
     *
     * @var string
     */
    private $message;
    
    /**
     * @param int $code
     * @param string $message
     */
    public function __construct($code, $message) {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
