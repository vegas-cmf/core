<?php
namespace Vegas\Tests\Stub\Services;

use Phalcon\Di\InjectionAwareInterface;
use Vegas\Di\InjectionAwareTrait;

class FakeService implements InjectionAwareInterface
{
    //do nothing
    use InjectionAwareTrait;
}
