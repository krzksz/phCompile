/*
 * This file is part of the phCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
var phCompile = (function(window, document, undefined) {
    var revert = function(className, attribute) {
        /**
         * Set default argument values.
         */
        className = className || 'ng-repeat';
        attribute = attribute || 'ng-repeat';

        /**
         * Remove all elements with given tag class.
         */
        var elements = document.getElementsByClassName(className);
        while(elements[0]) {
            elements[0].parentNode.removeChild(elements[0]);
        }

        /**
         * Replace element having given attribute contents with attribute's value.
         */
        elements = document.querySelectorAll('[' + attribute + ']');
        var elementsLength = elements.length;
        var textNode;
        for(var elementIndex = 0; elementIndex < elementsLength; elementIndex++) {
            textNode = document.createTextNode('{{' + elements[elementIndex].getAttribute(attribute) + '}}');
            elements[elementIndex].parentNode.replaceChild(textNode, elements[elementIndex]);
        }
        /**
         * Remove "ng-hide" class from first "ng-repeat" element.
         */
        elements = document.querySelectorAll('[ng-repeat]');
        elementsLength = elements.length;
        for(elementIndex = 0; elementIndex < elementsLength; elementIndex++) {
            elements[elementIndex].className = elements[elementIndex].className.replace(/(?:^|\s)ng-hide(?!\S)/g , '');
        }
    };

    return {
        revert : revert
    };
})(window, document);