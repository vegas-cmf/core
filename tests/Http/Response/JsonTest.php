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
 
namespace Vegas\Tests\Http\Response;

use Vegas\Http\Response\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{

    public function testResponseJsonSerialize()
    {
        $response = new Json();
        $response->success()->setData(array('test' => 1))->setMessage('Test message');
        $responseContentJson = json_encode($response);

        $testResponseArray = array(
            'success'   =>  true,
            'data' =>   array(
                'test'  =>  1
            ),
            'message'   =>  'Test message'
        );
        $testResponseJson = json_encode($testResponseArray);
        $this->assertEquals($testResponseJson, $responseContentJson);

        $response->fail();
        $responseContentJson = json_encode($response);
        $testResponseArray['success'] = false;
        $testResponseJson = json_encode($testResponseArray);
        $this->assertEquals($testResponseJson, $responseContentJson);
    }
} 