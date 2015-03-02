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
     * Indicates if parser should stop further rendering of current DOM element.
     *
     * @var bool
     */
    protected $haltParsing = false;

    /**
     * Creates new Renderer.
     *
     * @param PhRender $phRender PhRender object.
     */
    public function __construct(PhRender $phRender) {
        $this->phRender = $phRender;
    }

    /**
     * Returns boolean telling if parser should stop to render current DOM element.
     *
     * @return bool Tells parser if it should stop further rendering of certain
     * DOM element.
     */
    public function haltParsing() {
        return $this->haltParsing;
    }

    /**
     * Sets boolean value telling if parser should stop to render current DOM element.
     *
     * @param bool $haltParsing True if parsing should stop, false otherwise.
     */
    protected function setHaltParsing($haltParsing) {
        $this->haltParsing = $haltParsing;
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