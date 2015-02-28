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

use PhRender\Scope,
    PhRender\Template\Expression,
    PhRender\DOM\DOMUtils;

/**
 * Renders AngularJS ng-show and ng-hide attributes.
 */
class NgVisibility extends Renderer {

    /**
     * Renders AngularJS ng-show and ng-hide attributes by evaluating expression
     * inside them and setting "ng-hide" class if needed.
     *
     * @param \DOMElement $domElement DOM element to render.
     * @param Scope $scope Scope object containg data for expression.
     * @return \DOMElement Rendered DOM element.
     */
    public function render(\DOMElement $domElement, Scope $scope) {
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
        $expression = new Expression($this->phRender);
        $expressionValue = (bool)$expression->render($expressionString, $scope);

        /**
         * Set appropriate class to DOM element if needed.
         */
        if(($ngAttribute === 'ng-hide' && $expressionValue === true)
            || ($ngAttribute === 'ng-show' && $expressionValue === false)) {
            DOMUtils::addClass($domElement, 'ng-hide');
        }
        
        return $domElement;
    }
}