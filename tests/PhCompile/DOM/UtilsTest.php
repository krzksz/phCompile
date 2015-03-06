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
     * @covers PhCompile\DOM\Utils::loadHTML
     * @covers PhCompile\DOM\Utils::saveHTML
     */
    public function testLoadSaveHTML()
    {
        $source   = '<span>śćąłó©®℗™</span>';
        $document = Utils::loadHTML($source);

        $this->assertEquals($source, Utils::saveHTML($document));
    }

    /**
     * @covers PhCompile\DOM\Utils::loadHTMLFile
     * @depends testLoadSaveHTML
     */
    public function testLoadHTMLFile()
    {
        $filepath = TEST_PATH.'template/overall.html';
        $document = Utils::loadHTMLFile($filepath);

        $this->assertEquals(Utils::loadHTML(file_get_contents($filepath)),
            $document);
    }

    /**
     * @covers PhCompile\DOM\Utils::addClass
     * @dataProvider addClassProvider
     */
    public function testAddClass($className, $html, $expectedHtml)
    {
        $document = Utils::loadHTML($html);
        $element  = $document->getElementsByTagName('span')->item(0);

        Utils::addClass($element, $className);
        $renderedHtml = Utils::saveHTML($document);

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function addClassProvider()
    {
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
    public function testRemoveClass($className, $html, $expectedHtml)
    {
        $document = Utils::loadHTML($html);
        $element  = $document->getElementsByTagName('span')->item(0);

        Utils::removeClass($element, $className);
        $renderedHtml = Utils::saveHTML($document);

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function removeClassProvider()
    {
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
     * @covers PhCompile\DOM\DOMUtils::hasClass
     * @depends testAddClass
     */
    public function testHasClass()
    {
        $document = Utils::loadHTML('<span></span>');
        $element  = $document->getElementsByTagName('span')->item(0);

        $this->assertFalse(Utils::hasClass($element, 'foo'));
        Utils::addClass($element, 'foo');
        $this->assertTrue(Utils::hasClass($element, 'foo'));
    }

    /**
     * @covers PhCompile\DOM\DOMUtils::appendHtml
     * @dataProvider appendHtmlProvider
     */
    public function testAppendHTML($html, $appendHtml, $expectedHtml)
    {
        $document = Utils::loadHTML($html);
        $element  = $document->getElementsByTagName('span')->item(0);

        Utils::appendHTML($element, $appendHtml);
        $renderedHtml = Utils::saveHTML($document);

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function appendHtmlProvider()
    {
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