<?php
/*
 * This file is part of the ngPhRender package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhRender\Template\Renderer;

use PhRender\PhRender,
    PhRender\Scope;

/**
 * Start point for creating other Renderers.
 * Renderer objects are used for parsing and rendering AngularJS templates.
 */
abstract class Renderer {

    /**
     * PhRender object reference.
     *
     * @var PhRender
     */
    protected $phRender = null;

    /**
     * Creates new Renderer.
     *
     * @param PhRender $phRender PhRender object.
     */
    public function __construct(PhRender $phRender) {
        $this->phRender = $phRender;
    }

    /**
     * Renders and returns given DOM element using given Scope.
     *
     * @param \DOMElement $domElement DOM element to render.
     * @param Scope $scope Scope oblect containg data for rendering.
     * @return \DOMElement Renderer DOM element.
     */
    public function render(\DOMElement $domElement, Scope $scope) {
        return $domElement;
    }
}