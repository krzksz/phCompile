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
    PhRender\Template\Template,
    PhRender\DOM\DOMUtils;

/**
 * Renders AngularJS ng-show and ng-hide attributes.
 */
class Repeat extends Renderer {

    /**
     * Renders AngularJS ng-repeat attribute.
     * Each element copy is treated and rendered as seperate template with it's
     * own Scope data. Those elements are later appended to element's parrent node.
     *
     * @param \DOMElement $domElement DOM element to render.
     * @param Scope $scope Scope object.
     * @return void
     */
    public function render(\DOMElement $domElement, Scope $scope) {
        $parsedRepeat = $this->parseRepeat($domElement);
        $repeatObject = $scope->getData($parsedRepeat['object']);

        /**
         * Let's check if variable we're trying to enumerate is array.
         */
        if(is_array($repeatObject) === true) {
            foreach($repeatObject as $repeatKey => $repeatValue) {
                /**
                * For each element in array create proper Scope object.
                */
                if(empty($parsedRepeat['index']) === false) {
                    $scopeData = array(
                        $parsedRepeat['index'] =>  $repeatValue
                    );
                } else {
                    $scopeData = array(
                        $parsedRepeat['key'] => $repeatKey,
                        $parsedRepeat['value'] => $repeatValue
                    );
                }
                $subScope = new Scope($scope->getData());
                $subScope->setData($scopeData);
                /**
                 * Append subrendered DOM elelent.
                 */
                DOMUtils::appendHtml($domElement->parentNode, $this->subRender($domElement->cloneNode(true), $subScope));
                DOMUtils::addClass($domElement, 'ng-hide');

            }
        }
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
    protected function parseRepeat(\DOMElement $domElement) {
        $repeatString = $domElement->getAttribute('ng-repeat');
        preg_match('/(?:(?P<index>[^\s,\(\)]+)|\((?P<key>[^,]+)\s*,\s*(?P<value>[^\)]+)\))\s+in\s+(?P<object>[^\s,\(\)]+)/', $repeatString, $repeatMatch);

        return $repeatMatch;
    }

    /**
     * Renders given DOM element as subtemplate.
     *
     * @param \DOMElement $domElement DOM element to subrender.
     * @param Scope $scope Subtemplate Scope object.
     * @return string Rendered HTML.
     */
    protected function subRender(\DOMElement $domElement, $scope) {
        $template = new Template($this->phRender);
        /**
         * Remove ng-repeat attribute so we won't fall into infinite loop while parsing.
         */
        $domElement->removeAttribute('ng-repeat');
        /**
         * Tag element with render class, for easy client-side JavaScript manipulation.
         */
        DOMUtils::addClass($domElement, $this->phRender->getConfig('render.class'));
        $template->setHtml($domElement->ownerDocument->saveHTML($domElement));
        $template->setScope($scope);

        return $template->render(false);
    }
}