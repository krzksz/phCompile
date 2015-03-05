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
    PhCompile\DOM\DOMUtils,
    PhCompile\Template\Expression;

/**
 * Compiles AngularJS ng-bind attribute.
 */
class NgBind extends Directive{

    /**
     * Compiles AngularJS ng-bind attributes by evaluating expression inside it
     * and setting inner HTML.
     *
     * @param \DOMElement $domElement DOM element to compile.
     * @param Scope $scope Scope object containg data for expression.
     * @return \DOMElement Compiled DOM element.
     */
    public function compile(\DOMElement $domElement, Scope $scope) {
        $expressionString = $domElement->getAttribute('ng-bind');

        $expression = new Expression($this->phCompile);
        $expressionValue = $expression->compile($expressionString, $scope);

        if(empty($expressionValue) === false) {
            DOMUtils::appendHtml($domElement, $expressionValue);
        }

        return $domElement;
    }
}