<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Tag;

use Vegas\Tag\ShortenText;

class ShortenTextTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $shortenText = new ShortenText();

        $text = 'Abcdefg <strong>hijk</strong> test aksof';

        $this->assertEquals('Abcdefg hijk test aksof', $shortenText->prepare($text));
        $this->assertEquals('Abcdefg hijk...', $shortenText->prepare($text, 10));
        $this->assertEquals('Abcdefg hijk test ak123', $shortenText->prepare($text, 20, '123'));
    }
}