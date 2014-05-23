<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Db\Decorator\Helper;

use Phalcon\Utils\Slug;

/**
 * Class SlugTrait
 * @package Vegas\Db\Decorator
 */
trait SlugTrait
{
    /**
     * Creates a slug to be used for pretty URLs
     *
     * @link http://cubiq.org/the-perfect-php-clean-url-generator
     * @param         $string
     * @return mixed
     */
    public function generateSlug($string)
    {
        $slug = new Slug();
        $this->slug = $slug->generate($string);
    }
} 