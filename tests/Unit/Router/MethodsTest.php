<?php
namespace Tests\Unit\Router;

use Mixten\Route\Methods;
use PHPUnit\Framework\TestCase;

class MethodsTest extends TestCase
{
    public function testAnyMethodsArray()
    {
        $expected = ['GET', 'PUT', 'POST', 'HEAD', 'DELETE', 'CONNECT', 'OPTIONS'];

        $this->assertEquals($expected, Methods::any());
    }

    public function testGetMethodsArray()
    {
        $expected = ['GET', 'POST', 'OPTIONS'];

        $this->assertEquals($expected, Methods::get(...['GeT', 'POST', 'OPTIONS']));
        $this->assertEquals($expected, Methods::get(Methods::GET, Methods::POST, Methods::OPTIONS));
    }
}