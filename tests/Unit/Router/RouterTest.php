<?php
namespace Tests\Unit\Router;

use Canister\Canister;
use Mixten\Route\Methods;
use Mixten\Route\Route;
use Mixten\Route\RouteContainer;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testStoringRouteContainer()
    {
        $container = new Canister();
        $route_container = new RouteContainer($container);
        $route_container->register('/example', function() {
            return 'hello';
        }, Methods::get('get'), 'test');

        /** @var Route $route */
        $route = $route_container->getLastRoute();

        $this->assertEquals('/example', $route->getPath());
    }

    public function testRoutePersistencyInContainer()
    {
        $container = new Canister();
        $route_container = new RouteContainer($container);
        $route_container->register('/example', function() {
            return 'hello';
        }, Methods::get('get'), null, ['Fake', 'Ass', 'Middleware', 'Middleware']);

        /** @var Route $route */
        $route = $route_container->getLastRoute();

        $middleware = $route->getMiddleware();

        $this->assertArraySubset(['Fake', 'Ass', 'Middleware'], $middleware);
    }
}