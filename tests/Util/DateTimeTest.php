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

namespace Vegas\Tests\Util;

use Vegas\Util\DateTime;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldCreatedValidDateTimeObjectFromDateString()
    {
        $now = new \DateTime('now');
        $dateTime = \Vegas\Util\DateTime::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d H:i:s'));
        $this->assertInstanceOf('\DateTime', $dateTime);

        $this->assertEquals($now->format(DateTime::$globalDefaultFormat), (string) $dateTime);
        DateTime::$globalDefaultFormat = 'Y-m-d H:i';
        $this->assertEquals($now->format(DateTime::$globalDefaultFormat), (string) $dateTime);
        $dateTime->setDefaultFormat('Y-m-d');
        $this->assertNotEquals($now->format(DateTime::$globalDefaultFormat), (string) $dateTime);

        $dateTime->setDefaultFormat(false);
        DateTime::$globalDefaultFormat = false;
        $this->assertEmpty((string)$dateTime);
    }

    public function testShouldNotCreateValidDateTimeFromInvalidDateString()
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', 'Invalid date');
        $this->assertFalse($dateTime);
    }

    public function testShouldValidateGivenDateString()
    {
        $this->assertFalse(DateTime::isValid('Invalid date'));
        $this->assertFalse(DateTime::isValid(time()));
        $this->assertFalse(DateTime::isValid(null));

        $date = new \DateTime();

        // @see http://php.net/manual/en/datetime.formats.date.php
        if ($date->format('j') > 12) {
            $this->assertFalse(DateTime::isValid($date->format('d/m/Y')));
        } else {
            $this->assertTrue(DateTime::isValid($date->format('d/m/Y')));
        }

        $this->assertTrue(DateTime::isValid($date->format('Y-m-d H:i:s')));
        $this->assertTrue(DateTime::isValid($date->format('m/d/Y')));
    }

    public function testShouldSerializeDateTimeObjectToJson()
    {
        $now = new \DateTime('now');
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d H:i:s'));

        $this->assertEquals(json_encode($now->format(\DateTime::ISO8601)), json_encode($dateTime));
    }
}