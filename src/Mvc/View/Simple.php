<?php
/**
 * This file is part of Vegas package
 *
 * @author Jaroslaw Macko <jarek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Mvc\View;

use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Simple as PhalconSimpleView;

/**
 * Class Simple
 * @package Vegas\Mvc\View
 */
class Simple extends PhalconSimpleView
{

    /**
     * Constructor
     * Initializes simple view
     *
     * @param null $options
     */
    public function __construct($options=null) {
        parent::__construct($options);
        $simpleView = $this;
        $this->registerEngines(array(
            '.volt' => function ($simpleView, $di) use ($options) {
                $volt = new PhalconView\Engine\Volt($this, $di);
                $volt->setOptions(array(
                    'compiledPath' => $options['cacheDir'],
                    'compiledSeparator' => '_'
                ));
                return $volt;
            },
            '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
        ));
    }
}
