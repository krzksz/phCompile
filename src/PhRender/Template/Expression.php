<?php
/*
 * This file is part of the ngPhRender package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhRender\Template;

use PhRender\PhRender,
    PhRender\Scope;

/**
 * AngularJS' expressions compiler.
 *
 * This class is used to compile AngularJS' expressions like:
 * {{foo}}, {{foo.bar}}, {{foo+bar}}, {{2+1}} etc. with given Scope data.
 * Keep in mind that compilation uses eval() function and for security reasons
 * no function calls inside expressions are allowed.
 */
class Expression {

    /**
     * PhRender object reference.
     *
     * @var PhRender
     */
    protected $phRender = null;

    /**
     * Creates new Expression object.
     * This object is responsible for parsing and evaluating provided
     * AngularJS expressions.
     *
     * @param PhRender $phRender PhRender object.
     */
    public function __construct(PhRender $phRender) {
        $this->phRender = $phRender;
    }

    /**
     * Compiles given AngularJS expression with given Scope data.
     *
     * @param string $expression Expression to compile.
     * @param Scope $scope Scope object containing data for expression.
     * @return string Compiled expression.
     * @throws InvalidExpressionException Exception is thrown if expression contains brackets "()"
     * to prevent eval from calling functions for security reasons.
     */
    public function render($expression, Scope $scope) {
        $expression = trim($expression, '{} ');

        /**
         * We forbid any function calls inside expressions.
         */
        if(preg_match('/[\(\)]/', $expression) === 1) {
            throw new InvalidExpressionException(
                'Expression contains one or both of forbidden characters: "()"!'
            );
        }
        
        $expressionOperators = '/[^' . preg_quote('+-/*!=&|?:<>%,\'"', '/') . ']+/';

        preg_match_all($expressionOperators, $expression, $expressionMatches);
        
        /**
         * I no special operators found just replace access string with value.
         */
        if(isset($expressionMatches[0][0]) && $expressionMatches[0][0] === $expression) {
            $renderedExpression = $scope->getData($expression);
        } else {
            /**
             * Check each expression we can replace.
             */
            foreach($expressionMatches[0] as $expressionMatch) {
                $expressionMatch = trim($expressionMatch);


                /**
                 * If we can replace it with data - do this and format variable if required for later eval.
                 */
                if(is_numeric($expressionMatch) === false) {
                    $expression = str_replace($expressionMatch, var_export($scope->getData($expressionMatch), true), $expression);
                }
            }
            $renderedExpression = $this->evalaluate($expression);
        }

        return $renderedExpression;
    }

    /**
     * Evaluates expression and returns it's result.
     *
     * @param string $expression Expression to evaluate.
     * @return string Evaluated expression or empty string if eval error occured.
     */
    protected function evalaluate($expression) {
        $evaluated = @eval('return (' . $expression . ');');
        
        return $evaluated !== false ? $evaluated : '';
    }
}