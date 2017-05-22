<?php
namespace Tests\Unit;

use function Mixten\cc;
use Mixten\Exceptions\InvalidRouteMethod;
use function Mixten\parseMethods;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testCreateClosureString()
    {
        $expected = FunctionsTest::class . '::example';
        $this->assertEquals($expected, cc(FunctionsTest::class, 'example'));
    }

    /**
     * @expectedException InvalidRouteMethod
     */
    public function testInvalidMethodParseObject()
    {
        $this->expectException(InvalidRouteMethod::class);

        parseMethods(new \stdClass);
    }

    /**
     * @expectedException InvalidRouteMethod
     */
    public function testInvalidMethodParseEmptyArray()
    {
        $this->expectException(InvalidRouteMethod::class);

        parseMethods([]);
    }

    /**
     * @expectedException InvalidRouteMethod
     */
    public function testInvalidMethodParseNumber()
    {
        $this->expectException(InvalidRouteMethod::class);

        parseMethods(1);
    }

    public function testvalidMethodParse()
    {
        $this->assertEquals(['GET'], parseMethods('get'));
        $this->assertEquals(['GET'], parseMethods(['get']));
        $this->assertEquals(['GET'], parseMethods(['get', 'get']));
    }
}