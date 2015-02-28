<?php
/*
 * This file is part of the ngPhRender package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhRender;

use PhRender\Template\Renderer\Visibility,
    PhRender\DOM\DOMUtils;

class VisibilityTest extends \PHPUnit_Framework_TestCase
{
    protected $phRender;
    protected $visibiliy;
    protected $scope;

    public function setUp() {
        $this->phRender = new PhRender();
        $this->visibiliy = new Visibility($this->phRender);
        $this->scope = new Scope();
    }


    /**
     * @covers PhRender\Template\Renderer\Visibility::render
     * @dataProvider renderVisibleProvider
     */
    public function testRenderVisible($scopeData, $html, $expectedHtml) {
        $this->scope->setData($scopeData);
        
        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($html);
        $domElement = $domDocument->getElementsByTagName('span')->item(0);
        
        $renderedHtml = DOMUtils::saveHtml($this->visibiliy->render($domElement, $this->scope)->ownerDocument);
        
        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function renderVisibleProvider() {
        return array(
            array(
                array('foo' => true),
                '<span ng-show="{{foo}}"></span>',
                '<span ng-show="{{foo}}"></span>'
            ),
            array(
                array('foo' => true),
                '<span ng-show="bar"></span>',
                '<span ng-show="bar" class="ng-hide"></span>'
            )
        );
    }

    /**
     * @covers PhRender\Template\Renderer\Visibility::render
     * @dataProvider renderHiddenProvider
     */
    public function testRenderHidden($scopeData, $html, $expectedHtml) {
        $this->scope->setData($scopeData);

        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($html);
        $domElement = $domDocument->getElementsByTagName('span')->item(0);

        $renderedHtml = DOMUtils::saveHtml($this->visibiliy->render($domElement, $this->scope)->ownerDocument);

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function renderHiddenProvider() {
        return array(
            array(
                array('foo' => true),
                '<span ng-hide="{{foo}}"></span>',
                '<span ng-hide="{{foo}}" class="ng-hide"></span>'
            ),
            array(
                array('foo' => true),
                '<span ng-hide="bar"></span>',
                '<span ng-hide="bar"></span>'
            )
        );
    }
}