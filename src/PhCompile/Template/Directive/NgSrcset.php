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
    PhCompile\Template\Expression\Expression;

/**
 * Compiles AngularJS ng-srcset attribute.
 */
class NgSrcset extends Directive{

    /**
     * Creates new ng-src directive.
     *
     * @param PhCompile $phCompile PhCompile object.
     */
    public function __construct(PhCompile $phCompile)
    {
        parent::__construct($phCompile);
        $this->setName('ng-srcset');
        $this->setRestrict('A');
    }

    /**
     * Compiles AngularJS ng-srcset attributes by evaluating expression inside it
     * and setting srcset attribute.
     *
     * @param \DOMElement $domElement DOM element to compile.
     * @param Scope $scope Scope object containing data for expression.
     * @return \DOMElement Compiled DOM element.
     */
    public function compile(\DOMElement $domElement, Scope $scope) {
        $attrValue = $domElement->getAttribute('ng-srcset');

        $foundExpressions = array();
        $expression       = new Expression($this->phCompile);

        /**
         * Find all {{}} expressions.
         */
        preg_match_all('/{{([^}]+)}}/', $attrValue, $foundExpressions);
        foreach ($foundExpressions[1] as $foundExpression) {
            /**
             * Render and cover with span for easy client-site reverting.
             */
            $renderedExpression = $expression->compile($foundExpression,
                $scope);

            /**
             * Replace {{}} expression with rendered value.
             */
            $attrValue = str_replace('{{'.$foundExpression.'}}',
                $renderedExpression, $attrValue);
        }
        $domElement->setAttribute('srcset', $attrValue);

        return $domElement;
    }
}