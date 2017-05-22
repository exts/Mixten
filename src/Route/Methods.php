<?php
namespace Mixten\Route;

/**
 * Class RouteMethods
 *
 * @package Mixten\Route
 */
final class Methods
{
    /**
     * Method Constants
     */
    const GET = 'GET';
    CONST PUT = 'PUT';
    const POST = 'POST';
    const HEAD = 'HEAD';
    const DELETE = 'DELETE';
    const CONNECT = 'CONNECT';
    const OPTIONS = 'OPTIONS';

    /**
     * @return array
     */
    public static function any()
    {
        return self::get(self::GET, self::PUT, self::POST, self::HEAD, self::DELETE, self::CONNECT, self::OPTIONS);
    }

    /**
     * @param array ...$methods
     *
     * @return array
     */
    public static function get(...$methods)
    {
        return array_map(function($str) { return strtoupper($str); }, $methods) ?? [];
    }
}