<?php
/*
 * This file is part of the ngPhCompile package.
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
     * @param int $options Since PHP 5.4.0 and Libxml 2.6.0, you may also use the options parameter to specify additional Libxml parameters.
     * @return \DOMDocument|null Returns \DOMDocument on success, null if file doesn't exist.
     */
    public static function loadHTMLFile($filename, $options = 0)
    {
        $source = file_get_contents($filename);
        if ($source !== false) {
            return self::loadHTML($filename, $options = 0);
        }

        return null;
    }

    /**
     * Create DOM document form HTML string.
     * This custom implementation takes care of different character encodings
     * which standard DOMDocument has trouble with.
     *
     * @param string $source The HTML string.
     * @param int $options Since PHP 5.4.0 and Libxml 2.6.0, you may also use the options parameter to specify additional Libxml parameters.
     * @return \DOMDocument Returns \DOMDocument representing given HTML string.
     */
    public static function loadHTML($source, $options = 0)
    {
        $document = new \DOMDocument();
        /**
         * We have to surpress warnings of invalid HTML e.g. when giving only
         * fragments like "<span></span>" not entire valid document.
         */
        @$document->loadHTML(mb_convert_encoding($source, 'HTML-ENTITIES', 'UTF-8'),
            $options);

        return $document;
    }

    /**
     * Returns HTML representation of DOM document.
     * This method removes some additional HTML added by PHP's DOM parser if
     * given HTML is not entirely valid e.g. when parsing HTML chunks.
     *
     * @return string HTML representation of \DOMDocument document.
     */
    public static function saveHtml(\DOMDocument $document)
    {
        return html_entity_decode(trim(preg_replace('/<!DOCTYPE.+?>/', '',
                    str_replace(array('<html>', '</html>', '<body>', '</body>'),
                        array('', '', '', ''), $document->saveHTML()))));
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
     * Removes class from given \DOMElement. If class doesn't exists it does nothing.
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
        var_dump($html);
        $domFragment->appendXml($html);
        $domElement->appendChild($domFragment);

        return $domElement;
    }
}