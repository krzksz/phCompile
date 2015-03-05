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
    PhCompile\Template\Expression,
    PhCompile\Template\InvalidExpressionException;

/**
 * Compiles AngularJS ng-class attribute.
 */
class NgClass extends Directive
{

    /**
     * Compiles AngularJS ng-class attributes by evaluating expression inside it
     * and setting element's class attribute.
     *
     * @param \DOMElement $domElement DOM element to compile.
     * @param Scope $scope Scope object containg data for expression.
     * @return \DOMElement Compiled DOM element.
     */
    public function compile(\DOMElement $domElement, Scope $scope)
    {
        $classAttr = $domElement->getAttribute('ng-class');
        $classArray      = $this->parseClass($classAttr);

        if (isset($classArray['object']) && $classArray['object'] !== '') {
            $classString = $this->compileObject($classAttr, $scope);
        } elseif (isset($classArray['array']) && $classArray['array'] !== '') {
            $classString = $this->compilesArray($classAttr, $scope);
        } else {
            $classString = $this->compileString($classAttr, $scope);
        }

        if(empty($classString) === false && is_string($classString) === false) {
            throw new InvalidExpressionException(
                sprintf(
                    'Expression: "%s" inside ng-class does not evaluate to string!',
                    $classAttr
                )
            );
        }

        DOMUtils::addClass($domElement, $classString);

        return $domElement;
    }

    /**
     * Parses ng-class attribute.
     * This method parses expression inside ng-class attribute and splits
     * it using regular expression into array if needed.
     *
     * @param string $classAttr Ng-class attribute value.
     * @return array Parsed attribute expression.
     */
    protected function parseClass($classAttr)
    {
        preg_match('/^(?P<string>[^\[\]\,\{\}:\s]+)$'
            . '|^\[(?P<array>[^\]]+)\]$'
            . '|^{(?P<object>[^\}]+)}$/', $classAttr, $classMatches);

        return $classMatches;
    }

    /**
     * Compiles ng-class attribute as expression evaluating to string.
     *
     * @param string $classAttr Ng-class attribute value.
     * @param Scope $scope Scope object with data for current expression.
     * @return string Compiled class string.
     */
    protected function compileString($classAttr, $scope)
    {
        $expression = new Expression($this->phCompile);

        return $expression->compile($classAttr, $scope);
    }

    /**
     * Compiles ng-class attribute as expression evaluating to array containing strings.
     *
     * @param string $classAttr Ng-class attribute value.
     * @param Scope $scope Scope object with data for current expression.
     * @return string Compiled class string.
     */
    protected function compilesArray($classAttr, $scope)
    {
        $expression           = new Expression($this->phCompile);
        $classExpressionArray = explode(',', trim($classAttr, ' []'));
        $classString          = '';
        foreach ($classExpressionArray as $singleClassExpression) {
            $newClassString = $expression->compile(trim($singleClassExpression),
                    $scope);

            if(empty($newClassString) === false && is_string($newClassString) === false) {
                throw new InvalidExpressionException(
                    sprintf(
                        'Expression: "%s" inside ng-class does not evaluate to string!',
                        $classAttr
                    )
                );
            }

            $classString .= ' ' . $newClassString;
        }

        return trim($classString);
    }

    /**
     * Compiles ng-class attribute as expression evaluating to object with values
     * to evaluate and keys as class names.
     *
     * @param string $classAttr Ng-class attribute value.
     * @param Scope $scope Scope object with data for current expression.
     * @return string Compiled class string.
     */
    protected function compileObject($classAttr, $scope)
    {
        $expression = new Expression($this->phCompile);
        $classArray = explode(',', trim($classAttr, ' {}'));
        array_walk($classArray,
            function(&$singleExpression) {
            $singleExpression = explode(':', $singleExpression);
        });

        $classString = '';
        foreach ($classArray as $singleClassArray) {
            if ($expression->compile(trim($singleClassArray[1]), $scope) == true) {
                $classString .= ' '.trim($singleClassArray[0]);
            }
        }

        return trim($classString);
    }
}