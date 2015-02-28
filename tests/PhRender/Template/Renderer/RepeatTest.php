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

use PhRender\Template\Renderer\NgRepeat,
    PhRender\DOM\DOMUtils;

class RepeatTest extends \PHPUnit_Framework_TestCase
{
    protected $phRender;
    protected $repeat;
    protected $scope;

    public function setUp() {
        $this->phRender = new PhRender();
        $this->repeat = new NgRepeat($this->phRender);
        $this->scope = new Scope();
    }

    /**
     * @covers PhRender\Template\Renderer\Repeat::render
     */
    public function testRepeatArray() {
        $this->scope->setData(array(
            'bar'   =>  array(
                array(
                    'baz'   =>  'zaz'
                )
            )
        ));
        
        $domDocument = new \DOMDocument();
        $domDocument->loadHTML('<span ng-repeat="foo in bar">{{foo.baz}}</span>');
        $domElement = $domDocument->getElementsByTagName('span')->item(0);

        $renderClass = $this->phRender->getConfig('render.class');
        $renderAttr = $this->phRender->getConfig('render.attr');

        $this->repeat->render($domElement, $this->scope);
        $renderedHtml = DOMUtils::saveHtml($domDocument);
        
        $expectedHtml = '<span ng-repeat="foo in bar" class="ng-hide">{{foo.baz}}</span>';
        $expectedHtml .= '<span class="' . $renderClass . '"><span ' . $renderAttr . '="foo.baz">zaz</span></span>';

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    /**
     * @covers PhRender\Template\Renderer\Repeat::render
     */
    public function testRepeatObject() {
        $this->scope->setData(array(
            'bar'   =>  array(
                'baz'   =>  'zaz'
            )
        ));

        $domDocument = new \DOMDocument();
        $domDocument->loadHTML('<span ng-repeat="(key, value) in bar">{{key}}{{value}}</span>');
        $domElement = $domDocument->getElementsByTagName('span')->item(0);

        $renderClass = $this->phRender->getConfig('render.class');
        $renderAttr = $this->phRender->getConfig('render.attr');

        $this->repeat->render($domElement, $this->scope);
        $renderedHtml = DOMUtils::saveHtml($domDocument);

        $expectedHtml = '<span ng-repeat="(key, value) in bar" class="ng-hide">{{key}}{{value}}</span>';
        $expectedHtml .= '<span class="' . $renderClass . '"><span ' . $renderAttr . '="key">baz</span><span ' . $renderAttr . '="value">zaz</span></span>';

        $this->assertSame($expectedHtml, $renderedHtml);
    }
}