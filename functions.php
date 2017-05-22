<?php
namespace Mixten;

use Mixten\Exceptions\InvalidRouteMethod;

/**
 * Create a String Callable
 * eg.: cc(Example::class, 'test')
 * Also cleaner than Example::class . '::test', nicer to type as well.
 *
 * @param string $class
 * @param string $method
 *
 * @return string
 */
function cc(string $class, string $method)
{
    return sprintf('%s::%s', $class, $method);
}

/**
 * @param $methods
 *
 * @return array
 * @throws InvalidRouteMethod
 */
function parseMethods($methods)
{
    if(!is_string($methods) && !is_array($methods)) {
        throw new InvalidRouteMethod("The method you were trying to pass must be a string or an array");
    }

    if(is_string($methods)) {
        $methods = [$methods];
    }

    $valid_methods = ['CONNECT', 'DELETE', 'GET', 'HEAD', 'OPTIONS', 'POST', 'PUT'];

    //filter passed request methods so we can return valid ones
    $methods_array = [];
    foreach($methods as $method) {
        $method = strtoupper(trim($method));
        if(in_array($method, $valid_methods) && !in_array($method, $methods_array)) {
            $methods_array[] = $method;
        }
    }

    if(empty($methods_array)) {
        throw new InvalidRouteMethod(sprintf(
            "You must pass at least one of the following valid methods: %s",
            implode(', ', $valid_methods)
        ));
    }

    return $methods_array;
}