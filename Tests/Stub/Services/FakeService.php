<?php
namespace Vegas\Tests\Stub\Services;

use Phalcon\DI\InjectionAwareInterface;
use Vegas\DI\InjectionAwareTrait;

class FakeService implements InjectionAwareInterface
{
    //do nothing
    use InjectionAwareTrait;
}
