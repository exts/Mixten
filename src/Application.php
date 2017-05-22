<?php
namespace Mixten;

use Canister\Canister;
use Canister\CanisterInterface;
use Invoker\InvokerInterface;
use Mixten\Route\Methods;
use Mixten\Route\Route;
use Mixten\Route\RouteContainer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Starch\App;
use Starch\Middleware\Stack;
use Starch\Middleware\StackInterface;
use Zend\Diactoros\Response\EmitterInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

/**
 * Class Application
 *
 * @package Mixten
 */
final class Application
{
    /**
     * @var CanisterInterface
     */
    private $container;

    /**
     * @var App
     */
    private $app;

    /**
     * @var RouteContainer
     */
    private $router;

    /**
     * @var array
     */
    private $middleware = [];

    /**
     * Application constructor.
     *
     * @param CanisterInterface|null $container
     */
    public function __construct(CanisterInterface $container = null)
    {
        $this->container = $container ?? new Canister();
        $this->bootstrap();
    }

    /**
     * Setups container aliases & starch integration
     */
    public function bootstrap() : void
    {
        $this->container->alias(StackInterface::class, Stack::class);
        $this->container->alias(InvokerInterface::class, Invoker::class);
        $this->container->alias(EmitterInterface::class, SapiEmitter::class);

        $this->router = $this->container->get(RouteContainer::class);

        //setup starch framework
        $this->app = new App($this->container);
    }

    /**
     * @param $middleware
     */
    public function add($middleware)
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @param string $path
     * @param $callable
     * @param string|null $name
     * @param array $middleware
     *
     * @return RouteContainer
     */
    public function any(string $path, $callable, string $name = null, array $middleware = []) : RouteContainer
    {
        return $this->router->register($path, $callable, Methods::any(), $name, $middleware);
    }

    /**
     * @param string $path
     * @param $callable
     * @param string|null $name
     * @param array $middleware
     *
     * @return RouteContainer
     */
    public function get(string $path, $callable, string $name = null, array $middleware = []) : RouteContainer
    {
        return $this->router->register($path, $callable, Methods::get('get'), $name, $middleware);
    }

    /**
     * @param string $path
     * @param $callable
     * @param string|null $name
     * @param array $middleware
     *
     * @return RouteContainer
     */
    public function put(string $path, $callable, string $name = null, array $middleware = []) : RouteContainer
    {
        return $this->router->register($path, $callable, Methods::get('put'), $name, $middleware);
    }

    /**
     * @param string $path
     * @param $callable
     * @param string|null $name
     * @param array $middleware
     *
     * @return RouteContainer
     */
    public function post(string $path, $callable, string $name = null, array $middleware = []) : RouteContainer
    {
        return $this->router->register($path, $callable, Methods::get('post'), $name, $middleware);
    }

    /**
     * @param string $path
     * @param $callable
     * @param string|null $name
     * @param array $middleware
     *
     * @return RouteContainer
     */
    public function delete(string $path, $callable, string $name = null, array $middleware = []) : RouteContainer
    {
        return $this->router->register($path, $callable, Methods::get('delete'), $name, $middleware);
    }

    /**
     * @param string $path
     * @param $callable
     * @param array $methods
     * @param string|null $name
     * @param array $middleware
     *
     * @return RouteContainer
     */
    public function route(string $path, $callable, array $methods, string $name = null, array $middleware = []) : RouteContainer
    {
        return $this->router->register($path, $callable, Methods::get(...$methods), $name, $middleware);
    }

    /**
     * @param ServerRequestInterface|null $request
     * @param bool $return
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function run(ServerRequestInterface $request = null, $return = false)
    {
        //register general middleware
        foreach($this->middleware as $middleware) {
            $this->getStarchApp()->add($middleware);
        }

        //register routes
        /** @var Route $route */
        foreach($this->router->getRoutes() as $route) {
            //map route
            $this->getStarchApp()->map($route->getMethods(), $route->getPath(), $route->getCallable());

            //map middleware
            foreach($route->getMiddleware() as $middleware) {
                $this->getStarchApp()->add($middleware, $route->getPath());
            }
        }

        //globals
        $request = $request ?? ServerRequestFactory::fromGlobals();

        //run response throw middleware
        $response = $this->getStarchApp()->process($request);

        //return response (optional)
        if($return !== false) {
            return $response;
        }

        //handle response output
        $this->getStarchApp()->getContainer()->get(EmitterInterface::class)->emit($response);
    }

    /**
     * @return App
     */
    public function getStarchApp() : App
    {
        return $this->app;
    }

    /**
     * @return Canister|null|ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}