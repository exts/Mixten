<?php
namespace Mixten;

use Canister\Reflector;
use Invoker\InvokerInterface;

/**
 * Class Invoker
 *
 * @package Mixten
 */
class Invoker implements InvokerInterface
{
    /**
     * @var Reflector
     */
    private $reflector;

    /**
     * Invoker constructor.
     *
     * @param Reflector $reflector
     */
    public function __construct(Reflector $reflector)
    {
        $this->reflector = $reflector;
    }

    /**
     * @param callable $callable
     * @param array $parameters
     *
     * @return mixed
     */
    public function call($callable, array $parameters = array())
    {
        if(is_a($callable, \Closure::class)) {
            return $this->reflector->resolveCallable(null, $callable, $parameters);
        } else {
            return call_user_func_array($callable, $parameters);
        }
    }
}