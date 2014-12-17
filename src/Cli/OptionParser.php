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
 * Class OptionParser
 *
 * Command Line Interface (CLI) utility class.
 *
 * @package Vegas\Cli
 */
class OptionParser
{
    public static $args;

    /**
     * PARSE ARGUMENTS
     *
     * This command line option parser supports any combination of three types of options
     * [single character options (`-a -b` or `-ab` or `-c -d=dog` or `-cd dog`),
     * long options (`--foo` or `--bar=baz` or `--bar baz`)
     * and arguments (`arg1 arg2`)] and returns a simple array.
     *
     * [pfisher ~]$ php test.php --foo --bar=baz --spam eggs
     *   ["foo"]   => true
     *   ["bar"]   => "baz"
     *   ["spam"]  => "eggs"
     *
     * [pfisher ~]$ php test.php -abc foo
     *   ["a"]     => true
     *   ["b"]     => true
     *   ["c"]     => "foo"
     *
     * [pfisher ~]$ php test.php arg1 arg2 arg3
     *   [0]       => "arg1"
     *   [1]       => "arg2"
     *   [2]       => "arg3"
     *
     * [pfisher ~]$ php test.php plain-arg --foo --bar=baz --funny="spam=eggs" --also-funny=spam=eggs \
     * > 'plain arg 2' -abc -k=value "plain arg 3" --s="original" --s='overwrite' --s
     *   [0]       => "plain-arg"
     *   ["foo"]   => true
     *   ["bar"]   => "baz"
     *   ["funny"] => "spam=eggs"
     *   ["also-funny"]=> "spam=eggs"
     *   [1]       => "plain arg 2"
     *   ["a"]     => true
     *   ["b"]     => true
     *   ["c"]     => true
     *   ["k"]     => "value"
     *   [2]       => "plain arg 3"
     *   ["s"]     => "overwrite"
     *
     * Not supported: `-cd=dog`.
     *
     * @author              Patrick Fisher <patrick@pwfisher.com>
     * @since               August 21, 2009
     * @see                 https://github.com/pwfisher/CommandLine.php
     * @see                 http://www.php.net/manual/en/features.commandline.php
     *                      #81042 function arguments($argv) by technorati at gmail dot com, 12-Feb-2008
     *                      #78651 function getArgs($args) by B Crawford, 22-Oct-2007
     * @usage               $args = CommandLine::parseArgs($_SERVER['argv']);
     * @codeCoverageIgnore
     */
    public static function parse($argv = null)
    {
        $argv = $argv ? $argv : $_SERVER['argv'];
        array_shift($argv);
        $out = array();
        for ($i = 0, $j = count($argv); $i < $j; $i++) {
            $arg = $argv[$i];
            // --foo --bar=baz
            if (substr($arg, 0, 2) === '--') {
                $eqPos = strpos($arg, '=');
                // --foo
                if ($eqPos === false) {
                    $key = substr($arg, 2);
                    // --foo value
                    if ($i + 1 < $j && $argv[$i + 1][0] !== '-') {
                        $value = $argv[$i + 1];
                        $i++;
                    } else {
                        $value = isset($out[$key]) ? $out[$key] : true;
                    }
                    $out[$key] = $value;
                } // --bar=baz
                else {
                    $key = substr($arg, 2, $eqPos - 2);
                    $value = substr($arg, $eqPos + 1);
                    $out[$key] = $value;
                }
            } // -k=value -abc
            else if (substr($arg, 0, 1) === '-') {
                // -k=value
                if (substr($arg, 2, 1) === '=') {
                    $key = substr($arg, 1, 1);
                    $value = substr($arg, 3);
                    $out[$key] = $value;
                } // -abc
                else {
                    $chars = str_split(substr($arg, 1));
                    foreach ($chars as $char) {
                        $key = $char;
                        $value = isset($out[$key]) ? $out[$key] : true;
                        $out[$key] = $value;
                    }
                    // -a value1 -abc value2
                    if ($i + 1 < $j && $argv[$i + 1][0] !== '-') {
                        $out[$key] = $argv[$i + 1];
                        $i++;
                    }
                }
            } // plain-arg
            else {
                $value = $arg;
                $out[] = $value;
            }
        }
        self::$args = $out;
        return $out;
    }

    /**
     * GET BOOLEAN
     * @codeCoverageIgnore
     */
    public static function getBoolean($key, $default = false)
    {
        if (!isset(self::$args[$key])) {
            return $default;
        }
        $value = self::$args[$key];
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return (bool)$value;
        }
        if (is_string($value)) {
            $value = strtolower($value);
            $map = array(
                'y' => true,
                'n' => false,
                'yes' => true,
                'no' => false,
                'true' => true,
                'false' => false,
                '1' => true,
                '0' => false,
                'on' => true,
                'off' => false,
            );
            if (isset($map[$value])) {
                return $map[$value];
            }
        }
        return $default;
    }
} 