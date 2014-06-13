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

use Phalcon\DiInterface;
use Vegas\DI\ServiceProviderInterface;
use Vegas\DI\Scaffolding;

/**
 * Class ScaffoldingServiceProvider
 */
class FlashSessionServiceProvider implements ServiceProviderInterface
{
    const SERVICE_NAME = 'flash';

    /**
     * {@inheritdoc}
     */
    public function register(DiInterface $di)
    {
        $di->set(self::SERVICE_NAME, function() {
            return new \Phalcon\Flash\Session(array(
                'error' => 'flash-message flash-error',
                'success' => 'flash-message flash-success',
                'notice' => 'flash-message flash-info',
            ));
        });
    }

    public function getDependencies()
    {
        return array();
    }
}