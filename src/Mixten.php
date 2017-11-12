<?php
namespace Mixten;

use Canister\Canister;
use Canister\CanisterInterface;
use function DI\autowire;
use DI\Container;
use DI\ContainerBuilder;
use function DI\get;
use Invoker\InvokerInterface;
use Mixten\Route\Methods;
use Mixten\Route\Route;
use Mixten\Route\RouteContainer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Starch\App;
use Starch\Middleware\Stack;
use Starch\Middleware\StackInterface;
use Starch\Router\RouterMiddleware;
use Zend\Diactoros\Response\EmitterInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

/**
 * Class Mixten
 *
 * @package Mixten
 */
final class Mixten
{
    /**
     * @var RouteContainer
     */
    protected $router;

    /**
     * @var App
     */
    protected $starch;

    /**
     * @var ContainerBuilder
     */
    protected $builder;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $default_definitions = [];

    /**
     * @var array
     */
    protected $middleware = [];

    public function __construct()
    {
        $this->builder = new ContainerBuilder();
    }

    public function getBuilder()
    {
        return $this->builder;
    }

    public function setDefaultDefinitions(array $definitions = [])
    {
        if(empty($this->default_definitions) && !empty($definitions)) {
            $this->builder->addDefinitions($definitions);
        }
    }

    public function buildContainer()
    {
        $this->container = $this->builder->build();
    }

    public function bootstrap()
    {
        $this->setDefaultDefinitions([
            EmitterInterface::class => autowire(SapiEmitter::class),
            InvokerInterface::class => get(Container::class),
            StackInterface::class => autowire(Stack::class),
        ]);

        $this->buildContainer();

        //get router
        $this->router = $this->container->get(RouteContainer::class);

        //setup starch
        $this->starch = new App($this->container);
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

    public function run(ServerRequestInterface $request = null, $return = false)
    {
        foreach($this->middleware as $middleware) {
            $this->starch->add($middleware);
        }

        /** @var Route $route */
        foreach($this->router->getRoutes() as $route) {
            $this->starch->map($route->getMethods(), $route->getPath(), $route->getCallable());

            foreach($route->getMiddleware() as $middleware) {
                $this->starch->add($middleware, $route->getPath());
            }
        }

        //add default middleware
        $this->starch->add(RouterMiddleware::class);

        $request = $request ?? ServerRequestFactory::fromGlobals();
        $response = $this->starch->process($request);

        if($return === true) {
            return $response;
        }

        $this->starch->getContainer()->get(EmitterInterface::class)->emit($response);

        return null;
    }
}