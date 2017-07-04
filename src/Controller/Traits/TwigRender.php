<?php
namespace Mixten\Controller\Traits;

use Mixten\Renderer\Twig\TwigRenderer;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Trait TwigRender
 *
 * @package Mixten\Controller\Traits
 */
trait TwigRender
{
    /**
     * @param $template
     * @param array $options
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function render($template, array $options)
    {
        $render = $this->get(TwigRenderer::class);
        $response = new Response();
        $response->getBody()->write($render->render($template, $options));
        return $response;
    }
}