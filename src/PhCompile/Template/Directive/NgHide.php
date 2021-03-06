<?php
/*
 * This file is part of the phCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Template\Directive;

use PhCompile\PhCompile,
    PhCompile\Scope,
    PhCompile\Template\Expression\Expression,
    PhCompile\DOM\Utils;

/**
 * Compiles AngularJS ng-show and ng-hide attributes.
 */
class NgHide extends Directive
{

    /**
     * Creates new ng-show and ng-hide directive.
     *
     * @param PhCompile $phCompile PhCompile object.
     */
    public function __construct(PhCompile $phCompile)
    {
        parent::__construct($phCompile);
        $this->setName('ng-hide');
        $this->setRestrict('A');
    }

    /**
     * Renders AngularJS ng-show and ng-hide attributes by evaluating expression
     * inside them and setting "ng-hide" class if needed.
     *
     * @param \DOMElement $domElement DOM element to render.
     * @param Scope $scope Scope object containing data for expression.
     * @return \DOMElement Compiled DOM element.
     */
    public function compile(\DOMElement $domElement, Scope $scope)
    {
        $expressionString = $domElement->getAttribute('ng-hide');

        /**
         * Get attribute expression's value.
         */
        $expression      = new Expression($this->phCompile);
        $expressionValue = (bool) $expression->compile($expressionString, $scope);

        /**
         * Set appropriate class to DOM element if needed.
         */
        if ($expressionValue === true) {
            Utils::addClass($domElement, 'ng-hide');
        }

        return $domElement;
    }
}