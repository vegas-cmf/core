<?php
/**
 * Phalcon 1.3.2 Gettext adapter included in core differs from incubator version. Following __construct ovveride prevent
 * throwing "defaultDomain is required parameter" error.
 */
namespace Vegas\Translate\Adapter;

use Phalcon\Translate\Adapter;
use Phalcon\Translate\Adapter\Gettext As PhalconGettext;
use Phalcon\Translate\Exception;

class Gettext extends PhalconGettext
{
    public function __construct($options)
    {
        if (!is_array($options)) {
            throw new Exception('Invalid options');
        }

        if (!isset($options['locale'])) {
            throw new Exception('Parameter "locale" is required');
        }

        if (isset($options['domains'])) {
            unset($options['file']);
            unset($options['directory']);
        }

        if (!isset($options['domains']) && !isset($options['file'])) {
            throw new Exception('Option "file" is required unless "domains" is specified.');
        }

        if (!isset($options['domains']) && !isset($options['directory'])) {
            throw new Exception('Option "directory" is required unless "domains" is specified.');
        }

        if (isset($options['domains']) && !is_array($options['domains'])) {
            throw new Exception('If the option "domains" is specified it must be an array.');
        }

        putenv("LC_ALL=" . $options['locale']);
        setlocale(LC_ALL, $options['locale']);

        if (isset($options['domains'])) {
            foreach ($options['domains'] as $domain => $dir) {
                bindtextdomain($domain, $dir);
            }
            // set the first domain as default
            reset($options['domains']);
            $this->defaultDomain = key($options['domains']);
            // save list of domains
            $this->domains = array_keys($options['domains']);

        } else {
            if (is_array($options['file'])) {
                foreach ($options['file'] as $domain) {
                    bindtextdomain($domain, $options['directory']);
                }

                // set the first domain as default
                $this->defaultDomain = reset($options['file']);
                $this->domains = $options['file'];
            } else {
                bindtextdomain($options['file'], $options['directory']);
                $this->defaultDomain = $options['file'];
                $this->domains = array($options['file']);
            }
        }

        textdomain($this->defaultDomain);
    }
}
