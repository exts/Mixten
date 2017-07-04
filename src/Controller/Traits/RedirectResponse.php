<?php
namespace Mixten\Controller\Traits;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\RedirectResponse as ZendRedirectResponse;

/**
 * Trait RedirectResponse
 *
 * @package Mixten\Controller\Traits
 */
trait RedirectResponse
{
    /**
     * @param $path
     * @param int $status
     * @param array $headers
     *
     * @return ResponseInterface
     */
    public function redirect($path, $status = 302, array $headers = [])
    {
        return new ZendRedirectResponse($path, $status, $headers);
    }
}