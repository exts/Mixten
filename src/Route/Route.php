<?php
namespace Mixten\Route;

/**
 * Class Route
 *
 * @package Mixten\Route
 */
class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $methods;

    /**
     * @var callable
     */
    private $callable;

    /**
     * @var array
     */
    private $middleware = [];

    /**
     * Route constructor.
     *
     * @param $path
     * @param callable $callable
     * @param array $methods
     * @param string|null $name
     */
    public function __construct($path, callable $callable, array $methods, string $name = null)
    {
        $this->setName($name);
        $this->setPath($path);
        $this->setMethods($methods);
        $this->setCallable($callable);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName(?string $name)
    {
        if(isset($name)) {
            $this->name = $name;
        }
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param mixed $methods
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    /**
     * @return mixed
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @param mixed $callable
     */
    public function setCallable(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * @param array $middleware
     */
    public function setMiddleware(array $middleware)
    {
        if(!empty($middleware)) {
            foreach($middleware as $mw) {
                $this->addMiddleware($mw);
            }
        }
    }

    /**
     * @param $middleware
     */
    public function addMiddleware($middleware)
    {
        $name = $this->getName();
        if(isset($name) && !in_array($middleware, $this->middleware)) {
            $this->middleware[] = $middleware;
        }
    }
}