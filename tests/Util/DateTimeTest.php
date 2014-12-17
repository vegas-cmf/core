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

class DateTimeTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldCreatedValidDateTimeObjectFromDateString()
    {
        $now = new \DateTime('now');
        $dateTime = \Vegas\Util\DateTime::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d H:i:s'));
        $this->assertInstanceOf('\DateTime', $dateTime);

        $this->assertEquals($now->format(\Vegas\Util\DateTime::$globalDefaultFormat), (string) $dateTime);
        \Vegas\Util\DateTime::$globalDefaultFormat = 'Y-m-d H:i';
        $this->assertEquals($now->format(\Vegas\Util\DateTime::$globalDefaultFormat), (string) $dateTime);
        $dateTime->setDefaultFormat('Y-m-d');
        $this->assertNotEquals($now->format(\Vegas\Util\DateTime::$globalDefaultFormat), (string) $dateTime);

        $dateTime->setDefaultFormat(false);
        \Vegas\Util\DateTime::$globalDefaultFormat = false;
        $this->assertEmpty((string)$dateTime);
    }

    public function testShouldNotCreateValidDateTimeFromInvalidDateString()
    {
        $dateTime = \Vegas\Util\DateTime::createFromFormat('Y-m-d H:i:s', 'Invalid date');
        $this->assertFalse($dateTime);
    }

    public function testShouldValidateGivenDateString()
    {
        $this->assertFalse(\Vegas\Util\DateTime::isValid('Invalid date'));
        $this->assertFalse(\Vegas\Util\DateTime::isValid(time()));
        $this->assertFalse(\Vegas\Util\DateTime::isValid((new \DateTime())->format('d/m/Y')));
        $this->assertTrue(\Vegas\Util\DateTime::isValid((new \DateTime())->format('Y-m-d H:i:s')));
        $this->assertTrue(\Vegas\Util\DateTime::isValid((new \DateTime())->format('m/d/Y')));
    }

    public function testShouldSerializeDateTimeObjectToJson()
    {
        $now = new \DateTime('now');
        $dateTime = \Vegas\Util\DateTime::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d H:i:s'));

        $this->assertEquals(json_encode($now->format(\DateTime::ISO8601)), json_encode($dateTime));
    }
}