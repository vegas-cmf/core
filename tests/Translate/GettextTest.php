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
namespace Vegas\Tests\Tag;

use Vegas\Paginator\Adapter\Mongo;
use Vegas\Tag\Pagination;
use Vegas\Test\TestCase;
use Vegas\Tests\Stub\Models\FakeModel;
use Vegas\Tests\Stub\Models\FakePaginatorModel;
use Vegas\Translate\Adapter\Gettext;


class GettextTest extends TestCase
{
    public function testNotArrayArgument()
    {
        try {
            $gettext = new Gettext('not_an_array');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Phalcon\Translate\Exception', $ex);
        }

    }

    public function testLocaleArgument()
    {
        try {
            $gettext = new Gettext(array());
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Phalcon\Translate\Exception', $ex);
        }

    }

    public function testDomainArgument()
    {
        try {
            $gettext = new Gettext(array('locale' => 'en_US'));
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Phalcon\Translate\Exception', $ex);
        }

    }

    public function testDomainDirectoryArgument()
    {
        try {
            $gettext = new Gettext(array(
                'locale' => 'en_US',
                'directory' => '/tmp'
            ));
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Phalcon\Translate\Exception', $ex);
        }

    }

    public function testDomainFileArgument()
    {
        try {
            $gettext = new Gettext(array(
                'locale' => 'en_US',
                'file' => 'tmp.data'
            ));
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Phalcon\Translate\Exception', $ex);
        }

    }

    public function testDomainNotArrayArgument()
    {
        try {
            $gettext = new Gettext(array(
                'locale' => 'en_US',
                'file' => 'tmp.data',
                'directory' => '/tmp',
                'domains' => 'not_an_array'

            ));
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Phalcon\Translate\Exception', $ex);
        }

    }

    public function testDomainArrayArgument()
    {
        try {
            $gettext = new Gettext(array(
                'locale' => 'en_US',
                'file' => 'tmp.data',
                'directory' => '/tmp',
                'domains' => array(
                    'domain1', 'domain2'
                )

            ));
            throw new \Vegas\Exception('This exception');

        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Exception', $ex);
        }
    }

    public function testDomainFileAsArrayArgument()
    {
        try {
            $gettext = new Gettext(array(
                'locale' => 'en_US',
                'file' => array('tmp.data'),
                'directory' => '/tmp'

            ));
            throw new \Vegas\Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Exception', $ex);
        }

    }

}