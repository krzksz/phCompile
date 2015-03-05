<?php
/*
 * This file is part of the ngPhCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile;

use PhCompile\Template\Directive\NgVisibility,
    PhCompile\DOM\DOMUtils;

class VisibilityTest extends \PHPUnit_Framework_TestCase
{
    protected $phCompile;
    protected $visibiliy;
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->visibiliy = new NgVisibility($this->phCompile);
        $this->scope = new Scope();
    }


    /**
     * @covers PhCompile\Template\Directive\NgVisibility::compile
     * @dataProvider compileVisibleProvider
     */
    public function testCompileVisible($scopeData, $html, $expectedHtml) {
        $this->scope->setData($scopeData);
        
        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($html);
        $domElement = $domDocument->getElementsByTagName('span')->item(0);
        
        $renderedHtml = DOMUtils::saveHtml($this->visibiliy->compile($domElement, $this->scope)->ownerDocument);
        
        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function compileVisibleProvider() {
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
     * @covers PhCompile\Template\Directive\NgVisibility::compile
     * @dataProvider compileHiddenProvider
     */
    public function testCompileHidden($scopeData, $html, $expectedHtml) {
        $this->scope->setData($scopeData);

        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($html);
        $domElement = $domDocument->getElementsByTagName('span')->item(0);

        $renderedHtml = DOMUtils::saveHtml($this->visibiliy->compile($domElement, $this->scope)->ownerDocument);

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function compileHiddenProvider() {
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