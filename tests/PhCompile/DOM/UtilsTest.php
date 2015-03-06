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

class UtilsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers PhCompile\DOM\DOMUtils::addClass
     * @dataProvider addClassProvider
     */
    public function testAddClass($className, $html, $expectedHtml) {
        $domDocument = new Document();
        $domDocument->loadHTML($html);
        $domElement = $domDocument->getElementsByTagName('span')->item(0);

        Utils::addClass($domElement, $className);
        $renderedHtml = $domDocument->saveHtml();

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function addClassProvider() {
        return array(
            array(
                'foo',
                '<span></span>',
                '<span class="foo"></span>'
            ),
            array(
                'foo',
                '<span class=""></span>',
                '<span class="foo"></span>'
            ),
            array(
                'foo',
                '<span class="bar"></span>',
                '<span class="bar foo"></span>'
            )
        );
    }

    /**
     * @covers PhCompile\DOM\DOMUtils::removeClass
     * @dataProvider removeClassProvider
     */
    public function testRemoveClass($className, $html, $expectedHtml) {
        $domDocument = new Document();
        $domDocument->loadHTML($html);
        $domElement = $domDocument->getElementsByTagName('span')->item(0);

        Utils::removeClass($domElement, $className);
        $renderedHtml = $domDocument->saveHtml();

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function removeClassProvider() {
        return array(
            array(
                'foo',
                '<span></span>',
                '<span></span>'
            ),
            array(
                'foo',
                '<span class="foo"></span>',
                '<span class=""></span>'
            ),
            array(
                'foo',
                '<span class="bar foo"></span>',
                '<span class="bar"></span>'
            )
        );
    }

    /**
     * @covers PhCompile\DOM\DOMUtils::appendHtml
     * @dataProvider appendHtmlProvider
     */
    public function testAppendHtml($html, $appendHtml, $expectedHtml) {
        $domDocument = new Document();
        $domDocument->loadHTML($html);
        $domElement = $domDocument->getElementsByTagName('span')->item(0);

        Utils::appendHtml($domElement, $appendHtml);
        $renderedHtml = $domDocument->saveHtml();

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function appendHtmlProvider() {
        return array(
            array(
                '<span></span>',
                'foo',
                '<span>foo</span>'
            ),
            array(
                '<span>foo</span>',
                ' bar',
                '<span>foo bar</span>'
            ),
            array(
                '<span></span>',
                '<span>foo</span>',
                '<span><span>foo</span></span>'
            )
        );
    }
}
