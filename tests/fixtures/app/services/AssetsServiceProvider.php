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
use Vegas\Di\ServiceProviderInterface;

/**
 * Class AssetsServiceProvider
 */
class AssetsServiceProvider implements ServiceProviderInterface
{
    const SERVICE_NAME = 'assets';

    /**
     * {@inheritdoc}
     */
    public function register(DiInterface $di)
    {
        $di->set(self::SERVICE_NAME, '\Vegas\Assets\Manager', true);
    }

    public function getDependencies()
    {
        return array();
    }
}