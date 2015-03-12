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
    PhCompile\DOM\Utils,
    PhCompile\Template\Expression\Expression;

/**
 * Compiles AngularJS ng-value attribute.
 */
class NgValue extends Directive{

    /**
     * Creates new ng-value directive.
     *
     * @param PhCompile $phCompile PhCompile object.
     */
    public function __construct(PhCompile $phCompile)
    {
        parent::__construct($phCompile);
        $this->setName('ng-bind');
        $this->setRestrict('A');
    }

    /**
     * Compiles AngularJS ng-value attributes by evaluating expression inside it
     * and setting value attribute.
     *
     * @param \DOMElement $domElement DOM element to compile.
     * @param Scope $scope Scope object containing data for expression.
     * @return \DOMElement Compiled DOM element.
     */
    public function compile(\DOMElement $domElement, Scope $scope) {
        $expressionString = $domElement->getAttribute('ng-bind');

        $expression = new Expression($this->phCompile);
        $expressionValue = $expression->compile($expressionString, $scope);

        if(empty($expressionValue) === false) {
            Utils::appendHTML($domElement, $expressionValue);
        }

        return $domElement;
    }
}