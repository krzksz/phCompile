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
    PhRender\DOM\DOMUtils,
    PhRender\Template\Expression;

/**
 * Renders AngularJS ng-class attribute.
 */
class NgClass extends Renderer
{

    /**
     * Renders AngularJS ng-class attributes by evaluating expression inside it
     * and setting element's class attribute.
     *
     * @param \DOMElement $domElement DOM element to render.
     * @param Scope $scope Scope object containg data for expression.
     * @return \DOMElement Rendered DOM element.
     */
    public function render(\DOMElement $domElement, Scope $scope)
    {
        $classAttr = $domElement->getAttribute('ng-class');
        $classArray      = $this->parseClass($classAttr);

        if (isset($classArray['object']) && $classArray['object'] !== '') {
            $classString = $this->parseClassObject($classAttr, $scope);
        } elseif (isset($classArray['array']) && $classArray['array'] !== '') {
            $classString = $this->parseClassArray($classAttr, $scope);
        } else {
            $classString = $this->parseClassString($classAttr, $scope);
        }

        if(empty($classString) === false && is_string($classString) === false) {
            throw new \PhRender\Template\InvalidExpressionException(
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
     * Parses ng-class attribute as expression evaluating to string.
     *
     * @param string $classAttr Ng-class attribute value.
     * @param Scope $scope Scope object with data for current expression.
     * @return string Rendered class string.
     */
    protected function parseClassString($classAttr, $scope)
    {
        $expression = new Expression($this->phRender);

        return $expression->render($classAttr, $scope);
    }

    /**
     * Parses ng-class attribute as expression evaluating to array containing strings.
     *
     * @param string $classAttr Ng-class attribute value.
     * @param Scope $scope Scope object with data for current expression.
     * @return string Rendered class string.
     */
    protected function parseClassArray($classAttr, $scope)
    {
        $expression           = new Expression($this->phRender);
        $classExpressionArray = explode(',', trim($classAttr, ' []'));
        $classString          = '';
        foreach ($classExpressionArray as $singleClassExpression) {
            $newClassString = $expression->render(trim($singleClassExpression),
                    $scope);

            if(empty($newClassString) === false && is_string($newClassString) === false) {
                throw new \PhRender\Template\InvalidExpressionException(
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
     * Parses ng-class attribute as expression evaluating to object with values
     * to evaluate and keys as class names.
     *
     * @param string $classAttr Ng-class attribute value.
     * @param Scope $scope Scope object with data for current expression.
     * @return string Rendered class string.
     */
    protected function parseClassObject($classAttr, $scope)
    {
        $expression = new Expression($this->phRender);
        $classArray = explode(',', trim($classAttr, ' {}'));
        array_walk($classArray,
            function(&$singleExpression) {
            $singleExpression = explode(':', $singleExpression);
        });

        $classString = '';
        foreach ($classArray as $singleClassArray) {
            if ($expression->render(trim($singleClassArray[1]), $scope) == true) {
                $classString .= ' '.trim($singleClassArray[0]);
            }
        }

        return trim($classString);
    }
}