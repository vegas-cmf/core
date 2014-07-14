<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Http\Response;

/**
 * Class Json
 * @package Vegas\Http\Response
 */
class Json implements \JsonSerializable
{
    /**
     * Determines if request was succeed or failed
     *
     * @var bool
     */
    private $isSuccess = false;

    /**
     * Contains data
     *
     * @var string
     */
    private $data = '';

    /**
     * Response message
     *
     * @var string
     */
    private $message = '';

    /**
     * Determines if request was succeed
     *
     * @return $this
     */
    public function success()
    {
        $this->isSuccess = true;
        return $this;
    }

    /**
     * Determines if request was failed
     *
     * @return $this
     */
    public function fail()
    {
        $this->isSuccess = false;
        return $this;
    }

    /**
     * Sets request response message
     *
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Sets request data
     *
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Serializes array to JSON format
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return array(
            'success'   =>  $this->isSuccess,
            'data'      =>  $this->data,
            'message'     =>  $this->message
        );
    }
} 