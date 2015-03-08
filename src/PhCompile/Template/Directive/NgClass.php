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
    PhCompile\Template\Expression\Expression,
    PhCompile\Template\Expression\InvalidExpressionException;

/**
 * Compiles AngularJS ng-class attribute.
 */
class NgClass extends Directive
{

    /**
     * Creates new ng-class directive.
     *
     * @param PhCompile $phCompile PhCompile object.
     */
    public function __construct(PhCompile $phCompile)
    {
        parent::__construct($phCompile);
        $this->setName('ng-class');
        $this->setRestrict('A');
    }

    /**
     * Compiles AngularJS ng-class attributes by evaluating expression inside it
     * and setting element's class attribute.
     *
     * @param \DOMElement $element DOM element to compile.
     * @param Scope $scope Scope object containing data for expression.
     * @return \DOMElement Compiled DOM element.
     */
    public function compile(\DOMElement $element, Scope $scope)
    {
        $classAttr  = $element->getAttribute('ng-class');
        $classArray = $this->parseClass($classAttr);

        if (isset($classArray['object']) && $classArray['object'] !== '') {
            $classString = $this->compileObject($classAttr, $scope);
        } elseif (isset($classArray['array']) && $classArray['array'] !== '') {
            $classString = $this->compileArray($classAttr, $scope);
        } else {
            $classString = $this->compileString($classAttr, $scope);
        }

        if (empty($classString) === false && is_string($classString) === false) {
            throw new InvalidExpressionException(
            sprintf(
                'Expression: "%s" inside ng-class does not evaluate to string!',
                $classAttr
            )
            );
        }

        Utils::addClass($element, $classString);

        return $element;
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
            .'|^\[(?P<array>[^\]]+)\]$'
            .'|^{(?P<object>[^\}]+)}$/', $classAttr, $classMatches);

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
    protected function compileArray($classAttr, $scope)
    {
        $expression           = new Expression($this->phCompile);
        $classExpressionArray = explode(',', trim($classAttr, ' []'));
        $classString          = '';
        foreach ($classExpressionArray as $singleClassExpression) {
            $newClassString = $expression->compile(trim($singleClassExpression),
                $scope);

            if (empty($newClassString) === false && is_string($newClassString) === false) {
                throw new InvalidExpressionException(
                sprintf(
                    'Expression: "%s" inside ng-class does not evaluate to string!',
                    $classAttr
                )
                );
            }

            $classString .= ' '.$newClassString;
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