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

use PhCompile\Scope,
    PhCompile\Template\Expression\Expression,
    PhCompile\DOM\Utils;

/**
 * Compiles AngularJS ng-show and ng-hide attributes.
 */
class NgVisibility extends Directive {

    /**
     * Renders AngularJS ng-show and ng-hide attributes by evaluating expression
     * inside them and setting "ng-hide" class if needed.
     *
     * @param \DOMElement $domElement DOM element to render.
     * @param Scope $scope Scope object containg data for expression.
     * @return \DOMElement Compiled DOM element.
     */
    public function compile(\DOMElement $domElement, Scope $scope) {
        /**
         * Let's check if we are dealing with "ng-hide" or "ng-show" attribute.
         */
        if($domElement->hasAttribute('ng-hide')) {
            $expressionString = $domElement->getAttribute('ng-hide');
            $ngAttribute = 'ng-hide';
        } else {
            $expressionString = $domElement->getAttribute('ng-show');
            $ngAttribute = 'ng-show';
        }

        /**
         * Get attribute expression's value.
         */
        $expression = new Expression($this->phCompile);
        $expressionValue = (bool)$expression->compile($expressionString, $scope);

        /**
         * Set appropriate class to DOM element if needed.
         */
        if(($ngAttribute === 'ng-hide' && $expressionValue === true)
            || ($ngAttribute === 'ng-show' && $expressionValue === false)) {
            Utils::addClass($domElement, 'ng-hide');
        }
        
        return $domElement;
    }
}