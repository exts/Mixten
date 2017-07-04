<?php
namespace Mixten\Route;

use Mixten\Exceptions\InvalidRouteCallable;
use function Mixten\parseMethods;
use Psr\Container\ContainerInterface;

/**
 * Class RouteContainer
 *
 * @package Mixten\Route
 */
class RouteContainer
{
    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * RouteContainer constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $path
     * @param $callable
     * @param $methods
     * @param string|null $name
     * @param array $middleware
     *
     * @return RouteContainer
     * @throws InvalidRouteCallable
     */
    public function register(string $path, $callable, $methods, string $name = null, array $middleware = []) : self
    {
        if(!is_string($callable) && !is_callable($callable)) {
            throw new InvalidRouteCallable("The callable for path '%s' must be a callable "
                . "or a string that resolves from the container");
        }

        //check if we're passing a closure
        if(is_a($callable, \Closure::class)) {
            $this->routes[] = new Route($path, $callable, parseMethods($methods), $name);
            $this->addArray($middleware);
        } else {
            $callable = $this->processRouteCallable($path, $callable);
            $this->routes[] = new Route($path, $callable, parseMethods($methods), $name);
            $this->addArray($middleware);
        }

        return $this;
    }

    /**
     * @param $middleware
     *
     * @return RouteContainer
     */
    public function add($middleware) : self
    {
        /** @var Route $route */
        $route = $this->getLastRoute();
        if(isset($route)) {
            $route->addMiddleware($middleware);
        }

        return $this;
    }

    /**
     * @param array $middleware
     *
     * @return RouteContainer
     */
    public function addArray(array $middleware) : self
    {
        foreach($middleware as $value) {
            $this->add($value);
        }

        return $this;
    }

    /**
     * @param string $path
     * @param string $callable
     *
     * @return callable
     * @throws InvalidRouteCallable
     */
    private function processRouteCallable(string $path, string $callable) : callable
    {
        $callable = trim($callable);

        //check if there was any shared/factories or __invoke classes set first
        $current = $this->container->get($callable);
        if(is_callable($current)) {
            return $current;
        }

        //check if we're calling 'class::method' string and seeing if we can resolve it
        if(strpos($callable, '::') !== false) {

            //check container for existing class and if the method exists
            list($class, $method) = explode("::", $callable);
            if(!empty($method)) {
                $current = $this->container->get($class);
                if(is_object($current) && method_exists($current, $method)) {
                    return [$current, $method];
                }
            }

            //last check if it's a static callable by calling it directly
            if(is_callable($callable)) {
                return $callable;
            }
        }

        //throw exception
        throw new InvalidRouteCallable(sprintf(
            "The callable passed for '%s' isn't a valid callable",
            $path
        ));
    }

    /**
     * @return array
     */
    public function getRoutes() : array
    {
        return $this->routes;
    }

    /**
     * @return Route|null
     */
    public function getLastRoute() : ?Route
    {
        if(!empty($this->routes)) {
            return end($this->routes);
        }

        return null;
    }
}