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
 
namespace Vegas\Tests\Mvc\Dispatcher;

use Phalcon\Di;
use Phalcon\Http\Response;
use Vegas\Constants;
use Vegas\Mvc\Dispatcher\ExceptionResolver;
use Vegas\Mvc\View;

class ExceptionResolverTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        ob_start();
    }

    public function testLiveEnv()
    {
        $resolver = $this->getFreshResolver();

        $ex403 = new \Exception('Message 403', 403);
        $response = $resolver->resolve($ex403);

        $this->assertEquals('403 Access forbidden.', $response->getHeaders()->get('Status'));

        $ex404 = new \Exception('Message 404', 404);
        $response = $resolver->resolve($ex404);

        $this->assertEquals('404 The page does not exist.', $response->getHeaders()->get('Status'));

        $ex500 = new \Exception('Message 500', 500);
        $response = $resolver->resolve($ex500);

        $this->assertEquals('500 Application error.', $response->getHeaders()->get('Status'));

        $exCustom = new \Exception('Message custom', 123);
        $response = $resolver->resolve($exCustom);

        $this->assertEquals('400 Bad request.', $response->getHeaders()->get('Status'));
    }

    private function getFreshResolver($env = Constants::DEFAULT_ENV)
    {
        $di = Di::getDefault();
        $di->set('environment', function() use ($env) {
            return $env;
        }, true);

        $resolver = new ExceptionResolver();
        $resolver->setDI($di);

        return $resolver;
    }

    public function tearDown()
    {
        ob_end_clean();
    }
}