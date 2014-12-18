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

class ViewCacheServiceProvider implements \Vegas\DI\ServiceProviderInterface {

    const SERVICE_NAME = 'viewCache';


    /**
     * {@inheritdoc}
     */
    public function register(\Phalcon\DiInterface $di)
    {
        $config = $di->get('config');
        //Set the views cache service
        $di->set(self::SERVICE_NAME, function() use ($config) {

            //Cache data for one day by default
            $frontCache = new Phalcon\Cache\Frontend\Output([
                "lifetime" => 1
            ]);

            //File backend settings
            $cache = new Phalcon\Cache\Backend\File($frontCache, [
                "cacheDir" => $config->application->view->cacheDir
            ]);

            return $cache;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [];
    }
}
 