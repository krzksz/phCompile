<?php
/*
 * This file is part of the phCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\DOM;

/**
 * Utility class that provides some features missing from PHP's DOM implementation.
 */
class Utils
{
    /**
     * Create \DOMDocument form HTML file.
     * This custom implementation takes care of different character encodings
     * which standard DOMDocument has trouble with.
     *
     * @param string $filename The path to the HTML file.
     * @return \DOMDocument|null Returns \DOMDocument on success, null if file doesn't exist.
     */
    public static function loadHTMLFile($filename)
    {
        $source = file_get_contents($filename);
        if ($source !== false) {
            return self::loadHTML($source);
        }

        return null;
    }

    /**
     * Create DOM document form HTML string.
     * This custom implementation takes care of different character encodings
     * which standard DOMDocument has trouble with.
     *
     * @param string $source The HTML string.
     * @return \DOMDocument Returns \DOMDocument representing given HTML string.
     */
    public static function loadHTML($source)
    {
        $document = new \DOMDocument();
        /**
         * We have to suppress warnings of invalid HTML e.g. when giving only
         * fragments like "<span></span>" not entire valid document.
         */
        $document->loadHTML(mb_convert_encoding($source, 'HTML-ENTITIES', 'UTF-8'));

        return $document;
    }

    /**
     * Returns HTML representation of DOM document.
     * This method removes some additional HTML added by PHP's DOM parser if
     * given HTML is not entirely valid e.g. when parsing HTML chunks.
     *
     * @param \DOMDocument $document DOM document to convert to HTML.
     * @return string HTML representation of \DOMDocument document.
     */
    public static function saveHTML(\DOMDocument $document)
    {
        return html_entity_decode(
            trim(
                preg_replace(
                    '/<!DOCTYPE.+?>/', '',
                    str_replace(array('<html>', '</html>', '<body>', '</body>'),
                        array('', '', '', ''), $document->saveHTML())
                )
            ), ENT_COMPAT, 'UTF-8');
    }


    /**
     * Adds class to \DOMElement. If class already exists it does nothing.
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
     * Removes class from given \DOMElement. If class does not exist it does nothing.
     *
     * @param \DOMElement $domElement DOM element to remove class from.
     * @param string $className Class name to remove.
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
     * Tells if given DOM element has given class.
     *
     * @param \DOMElement $domElement DOM element to search in.
     * @param string $className Class name to search for.
     * @return boolean True if DOM element has given class, false otherwise.
     */
    public static function hasClass(\DOMElement $domElement, $className)
    {
        return strpos($domElement->getAttribute('class'), $className) !== false;
    }

    /**
     * Appends given HTML inside given DOM element.
     *
     * @param \DOMElement $domElement DOM element to with contents HTML should be appended.
     * @param string $html HTML to append.
     * @return \DOMElement DOM element with appended contents.
     */
    public static function appendHTML(\DOMElement $domElement, $html)
    {
        $domFragment = $domElement->ownerDocument->createDocumentFragment();
        $domFragment->appendXml($html);
        $domElement->appendChild($domFragment);

        return $domElement;
    }
}