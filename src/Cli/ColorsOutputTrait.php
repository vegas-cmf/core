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

namespace Vegas\Cli;

/**
 * Class ColorsOuputTrait
 * @package Vegas\Cli
 */
trait ColorsOutputTrait
{
    /**
     * @var array
     * @internal
     */
    private $foregroundColors = array();

    /**
     * @var array
     * @internal
     */
    private $backgroundColors = array();

    /**
     * @TODO Task constructor is final in Phalcon 2.0, need to move this
     *
     * Constructors
     * Defines available colors
     */
    /*public function __construct()
    {
        // Set up shell colors
        $this->foregroundColors['black'] = '0;30';
        $this->foregroundColors['dark_gray'] = '1;30';
        $this->foregroundColors['blue'] = '0;34';
        $this->foregroundColors['light_blue'] = '1;34';
        $this->foregroundColors['green'] = '0;32';
        $this->foregroundColors['light_green'] = '1;32';
        $this->foregroundColors['cyan'] = '0;36';
        $this->foregroundColors['light_cyan'] = '1;36';
        $this->foregroundColors['red'] = '0;31';
        $this->foregroundColors['light_red'] = '1;31';
        $this->foregroundColors['purple'] = '0;35';
        $this->foregroundColors['light_purple'] = '1;35';
        $this->foregroundColors['brown'] = '0;33';
        $this->foregroundColors['yellow'] = '1;33';
        $this->foregroundColors['light_gray'] = '0;37';
        $this->foregroundColors['white'] = '1;37';

        $this->backgroundColors['black'] = '40';
        $this->backgroundColors['red'] = '41';
        $this->backgroundColors['green'] = '42';
        $this->backgroundColors['yellow'] = '43';
        $this->backgroundColors['blue'] = '44';
        $this->backgroundColors['magenta'] = '45';
        $this->backgroundColors['cyan'] = '46';
        $this->backgroundColors['light_gray'] = '47';
    }*/

    /**
     * Returns colored string
     *
     * @param $string
     * @param null $foregroundColor
     * @param null $backgroundColor
     * @return string
     */
    public function getColoredString($string, $foregroundColor = null, $backgroundColor = null) {

        $colored_string = "";

        // Check if given foreground color found
        if (isset($this->foregroundColors[$foregroundColor])) {
            $colored_string .= "\033[" . $this->foregroundColors[$foregroundColor] . "m";
        }
        // Check if given background color found
        if (isset($this->backgroundColors[$backgroundColor])) {
            $colored_string .= "\033[" . $this->backgroundColors[$backgroundColor] . "m";
        }

        // Add string and end coloring
        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }
}
