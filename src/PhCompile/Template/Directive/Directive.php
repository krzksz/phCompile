<?php
/*
 * This file is part of the ngPhCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Template\Directive;

use PhCompile\PhCompile,
    PhCompile\Scope;

/**
 * Start point for creating other Directives.
 * Directive objects are used for parsing DOM elements with given data.
 */
abstract class Directive
{
    /**
     * PhCompile object reference.
     *
     * @var PhCompile
     */
    protected $phCompile = null;

    /**
     * Indicates if parser should stop further rendering of current DOM element.
     *
     * @var bool
     */
    protected $haltCompiling = false;

    /**
     * Creates new Directive.
     *
     * @param PhCompile $phCompile PhRender object.
     */
    public function __construct(PhCompile $phCompile)
    {
        $this->phCompile = $phCompile;
    }

    /**
     * Renders and returns given DOM element using given Scope.
     *
     * @param \DOMElement $domElement DOM element to render.
     * @param Scope $scope Scope oblect containg data for rendering.
     * @return \DOMElement Renderer DOM element.
     */
    public function compile(\DOMElement $domElement, Scope $scope)
    {
        return $domElement;
    }

    /**
     * Returns boolean telling if parser should stop compiling current DOM element.
     *
     * @return bool Tells compiler if it should stop further compiling of certain
     * DOM element.
     */
    public function haltCompiling()
    {
        return $this->haltCompiling;
    }

    /**
     * Sets boolean value telling if parser should stop compiling current DOM element.
     *
     * @param bool $haltCompiling True if compiling should stop, false otherwise.
     */
    protected function setHaltCompiling($haltCompiling)
    {
        $this->haltCompiling = $haltCompiling;
    }
}