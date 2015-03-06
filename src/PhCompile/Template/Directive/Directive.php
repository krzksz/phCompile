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
     * Directive for attributes.
     */
    const RESTRICT_A = 1;
    /**
     * Directive for elements.
     */
    const RESTRICT_E = 2;
    /**
     * Directive for classes.
     */
    const RESTRICT_C = 4;

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
     * Directive restriction, can be element, attribute or class.
     *
     * @var int
     */
    protected $restrict;

    protected $prioriy = 0;

    /**
     * Creates new directive.
     *
     * @param PhCompile $phCompile PhRender object.
     */
    public function __construct(PhCompile $phCompile)
    {
        $this->phCompile = $phCompile;
        $this->restrict = self::RESTRICT_A | self::RESTRICT_C | self::RESTRICT_E;
    }

    /**
     * Returns directive restriction.
     *
     * @return int Directive restriction.
     */
    public function getRestrict() {
        return $this->restrict;
    }

    /**
     * Sets directive restriction.
     *
     * @param int $restrict New directive restriction.
     */
    public function setRestrict($restrict) {
        $this->restrict = $restrict;
    }

    /**
     * Sets directive priority.
     * Default directive priority is 0.
     *
     * @param int $priority New directive priority.
     */
    public function setPriority(int $priority) {
        $this->prioriy = $priority;
    }

    /**
     * Returns directive priority.
     *
     * @return int
     */
    public function getPriority() {
        return $this->prioriy;
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