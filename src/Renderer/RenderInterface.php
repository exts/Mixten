<?php
namespace Mixten\Renderer;

/**
 * Interface RenderInterface
 *
 * @package Mixten\Renderer
 */
interface RenderInterface
{
    /**
     * @param $template
     * @param array $opts
     *
     * @return mixed
     */
    public function render($template, array $opts = []);
}