<?php
/**
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright (c) 2015, Amsterdam Standard
 */

namespace Vegas\Bootstrap;

use Phalcon\Config;
use Phalcon\Debug;
use Vegas\Constants;

trait ErrorHandlerInitializerTrait
{
    protected $debug;

    /**
     * Initializes error handling
     */
    public function initErrorHandler(Config $config)
    {
        if ($this->getDI()->get('environment') === Constants::PROD_ENV) {
            return;
        }

        $this->debug = new Debug();
        $this->debug->listen();

        set_error_handler([$this, 'errorHandler'], error_reporting());

        register_shutdown_function(function() {
            if ((error_reporting() & E_ERROR) === E_ERROR) {
                $lastError = error_get_last();

                if ($lastError['type'] === E_ERROR) {
                    // fatal error
                    $this->errorHandler(E_ERROR, $lastError['message'], $lastError['file'], $lastError['line']);
                }
            }
        });
    }

    /**
     * Create default error handler.
     *
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     */
    public function errorHandler($code, $message, $file, $line)
    {
        $this->debug->setShowBackTrace(false);
        $this->debug->onUncaughtException(new \ErrorException($message, $code, 0, $file, $line));
    }


    /**
     * @return mixed
     */
    abstract public function getDI();
}
