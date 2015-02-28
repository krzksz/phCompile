<?php
/*
 * This file is part of the ngPhRender package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhRender\DOM;

/**
 * Utility class that provides some features missing from PHP's DOM implementation.
 */
class DOMUtils
{

    /**
     * Adds class to DOM element. If class already exists it does nothing.
     *
     * @param \DOMElement $domElement DOM element to add class to.
     * @param string $className Class name to add.
     */
    public static function addClass(\DOMElement $domElement, $className)
    {
        self::removeClass($domElement, $className);
        $domElement->setAttribute('class',
            trim($domElement->getAttribute('class').' '.$className));
    }

    /**
     * Removes class from given DOM element. If class doesn't exists it does nothing.
     *
     * @param \DOMElement $domElement DOM element to remove class from.
     * @param type $className Class name to remove.
     */
    public static function removeClass(\DOMElement $domElement, $className)
    {
        if ($domElement->hasAttribute('class')) {
            $domElement->setAttribute('class',
                trim(str_replace($className, '',
                        $domElement->getAttribute('class'))));
        }
    }

    /**
     * Appends given HTML inside given DOM element.
     *
     * @param \DOMElement $domElement DOM element to with contents HTML should be appended.
     * @param type $html HTML to append.
     * @return \DOMElement DOM element with appended contents.
     */
    public static function appendHtml(\DOMElement $domElement, $html)
    {
        $domFragment = $domElement->ownerDocument->createDocumentFragment();
        $domFragment->appendXml($html);
        $domElement->appendChild($domFragment);

        return $domElement;
    }

    /**
     * Returns HTML representation of given DOM.
     * This method removes some additional HTML added by PHP's DOM parser if
     * given HTML is not entirely valid e.g. when parsing HTML chunks.
     *
     * @param \DOMDocument $domDocument DOM which HTML representation we want.
     * @return string HTML representation of given DOM.
     */
    public static function saveHtml(\DOMDocument $domDocument)
    {
        return trim(preg_replace('/<!DOCTYPE.+?>/', '',
                str_replace(array('<html>', '</html>', '<body>', '</body>'),
                    array('', '', '', ''), $domDocument->saveHTML())));
    }
}