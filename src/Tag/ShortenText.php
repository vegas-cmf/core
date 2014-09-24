<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
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
     * Cuts text to specified length and appends end string.
     *
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
