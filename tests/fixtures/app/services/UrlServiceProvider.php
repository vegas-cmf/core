<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phalcon\DiInterface;
use Phalcon\Mvc\Url as UrlResolver;
use Vegas\DI\ServiceProviderInterface;

/**
 * Class UrlServiceProvider
 */
class UrlServiceProvider implements ServiceProviderInterface
{
    const SERVICE_NAME = 'url';

    /**
     * {@inheritdoc}
     */
    public function register(DiInterface $di)
    {
        $di->set(self::SERVICE_NAME, function() use ($di) {
            $url = new UrlResolver();
            $url->setBaseUri($di->get('config')->application->baseUri);
            return $url;
        }, true);
    }

    public function getDependencies()
    {
        return array();
    }
} 