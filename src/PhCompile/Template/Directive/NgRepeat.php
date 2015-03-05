<?php
/*
 * This file is part of the ngPhRender package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Template\Directive;

use PhCompile\Scope,
    PhCompile\Template\Template,
    PhCompile\DOM\DOMUtils;

/**
 * Compiles AngularJS ng-show and ng-hide attributes.
 */
class NgRepeat extends Directive
{

    /**
     * Compiles AngularJS ng-repeat attribute.
     * Each element's copy is treated and rendered as seperate template with it's
     * own Scope data. Those elements are later appended to element's parent node.
     *
     * @todo Think of refactoring methods.
     * @todo Implement expression cache for better performance.
     *
     * @param \DOMElement $domElement DOM element to compile.
     * @param Scope $scope Scope object.
     * @return void
     */
    public function compile(\DOMElement $domElement, Scope $scope)
    {
        $parsedArray = $this->parseRepeat($domElement);
        $repeatArray = $scope->getData($parsedArray['array']);
        /**
         * Reset halt compiling to it's default value.
         */
        $this->setHaltCompiling(false);

        /**
         * Let's check if variable we're trying to enumerate is array.
         */
        if (is_array($repeatArray) === true) {
            $repeatCount = count($repeatArray);
            $repeatIndex = 0;
            foreach ($repeatArray as $repeatKey => $repeatValue) {
                $subScope = new Scope($scope->getData());
                $this->setScopeData($subScope, $parsedArray, $repeatKey, $repeatValue);
                $this->setScopeSpecial($subScope, $repeatCount, $repeatIndex);

                /**
                 * Append subcompiled DOM elelent.
                 */
                DOMUtils::appendHtml($domElement->parentNode,
                    $this->subcompile($domElement->cloneNode(true), $subScope));
                $repeatIndex++;
            }
            /**
             * We stop further compiling of source DOM element, we want it to
             * be intact and hidden so we can replace it back on the client side.
             */
            $this->setHaltCompiling(true);
            DOMUtils::addClass($domElement, 'ng-hide');
        }
    }

    /**
     * Sets data for given scope based on given values from current ng-repeat
     * cycle.
     *
     * @param Scope $scope Scope to set data to.
     * @param array $parsedArray Parsed ng-repeat attribute.
     * @param string $repeatKey Key of the current element of the array we iterate over.
     * @param type $repeatValue Value of the current element of the array we iterate over.
     * @return Scope Scope object with set values.
     */
    protected function setScopeData(Scope $scope, $parsedArray, $repeatKey, $repeatValue)
    {
        if (empty($parsedArray['index']) === false) {
            $scopeData = array(
                $parsedArray['index'] => $repeatValue
            );
        } else {
            $scopeData = array(
                $parsedArray['key'] => $repeatKey,
                $parsedArray['value'] => $repeatValue
            );
        }
        $scope->setData($scopeData);

        return $scope;
    }

    /**
     * Sets special properties for given scope based on current ng-repeat cycle.
     *
     * @param Scope $scope Scope to set data to.
     * @param int $repeatCount Size of the array we iterate over.
     * @param int $repeatIndex Current index of the array we iterate over.
     * @return Scope Scope object with set values.
     */
    protected function setScopeSpecial(Scope $scope, $repeatCount, $repeatIndex)
    {
        $scopeData['$index'] = $repeatIndex;
        $scopeData['$first'] = ($repeatIndex === 0);
        $scopeData['$last'] = ($repeatIndex === $repeatCount - 1);
        $scopeData['$middle'] = !($scopeData['$first'] || $scopeData['$last']);
        $scopeData['$even'] = ($repeatIndex % 2 === 0);
        $scopeData['$odd'] = !($scopeData['$even']);
        $scope->setData($scopeData);

        return $scope;
    }

    /**
     * Parses ng-repeat expression.
     * This method parses ng-repeat expression. ex. "foo in bar", "(foo, bar) in baz"
     * into array for later use.
     *
     * "foo in bar" turns into:
     * array(
     *     "index"   => "foo",
     *     "object"  => "bar"
     * )
     *
     * "(foo, bar) in baz" turns into:
     * array(
     *     "key"     => "foo",
     *     "value"   => "bar",
     *     "object"  => "baz"
     * )
     *
     * @param \DOMElement $domElement DOM element which ng-repeat to parse.
     * @return array Parsed ng-repeat expression.
     */
    protected function parseRepeat(\DOMElement $domElement)
    {
        $repeatString = $domElement->getAttribute('ng-repeat');
        preg_match('/(?:(?P<index>[^\s,\(\)]+)|\((?P<key>[^,]+)\s*,\s*(?P<value>[^\)]+)\))\s+in\s+(?P<array>[^\s,\(\)]+)/',
            $repeatString, $repeatMatch);

        return $repeatMatch;
    }

    /**
     * Renders given DOM element as subtemplate.
     *
     * @param \DOMElement $domElement DOM element to subrender.
     * @param Scope $scope Subtemplate Scope object.
     * @return string Rendered HTML.
     */
    protected function subcompile(\DOMElement $domElement, $scope)
    {
        $template = new Template($this->phCompile);
        /**
         * Remove ng-repeat attribute so we won't fall into infinite loop while parsing.
         */
        $domElement->removeAttribute('ng-repeat');
        /**
         * Tag element with render class, for easy client-side JavaScript manipulation.
         */
        DOMUtils::addClass($domElement,
            $this->phCompile->getConfig('compile.class'));
        $template->setHtml($domElement->ownerDocument->saveHTML($domElement));
        $template->setScope($scope);

        return $template->compile(false);
    }
}