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
use Vegas\Session\Adapter\Files as SessionAdapter;

class I18nServiceProvider implements ServiceProviderInterface
{
    const SERVICE_NAME = 'i18n';

    /**
     * {@inheritdoc}
     */
    public function register(DiInterface $di)
    {
        $config = $di->get('config');
        $di->set('i18n', function() use ($config) {
            return new \Vegas\Translate\Adapter\Gettext(array(
                'locale' => $config->application->language,
                'file' => 'messages',
                'directory' => APP_ROOT.'/lang'
            ));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return array();
    }
} 