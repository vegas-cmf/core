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

namespace Vegas\Tag;

/**
 * Class ShortenText
 * @package Vegas\Tag
 */
class ShortenText
{
    /**
     * @param $text
     * @param int $length
     * @param string $endString
     * @return string
     */
    public function prepare($text, $length = 100, $endString = '...')
    {
        $substring = strip_tags(preg_replace('/<br.?\/?>/', ' ',$text));
        $textLength = mb_strlen($substring);
        
        if($textLength > $length) {
            $lastWordPosition = mb_strpos($substring, ' ', $length);
            
            if($lastWordPosition) {
                $substring = mb_substr($substring, 0, $lastWordPosition);
            } else {
                $substring = mb_substr($substring, 0, $length);
            }
            
            $substring.= $endString;
        }
        
        return $substring;
    }
}
