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
     * @covers PhRender\Template\Renderer\NgRepeat::render
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
     * @covers PhRender\Template\Renderer\NgRepeat::render
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

    /**
     * @covers PhRender\Template\Renderer\NgRepeat::render
     * @dataProvider repeatSpectialPropertiesProvider
     */
    public function testRepeatSpecialProperties($expression, $expectedArray) {
        $this->scope->setData(array(
            'bar'   =>  array(  1, 2, 3, 4, 5, 6)
        ));

        $domDocument = new \DOMDocument();
        $domDocument->loadHTML('<span ng-repeat="n in bar">{{' . $expression . '}}</span>');
        $domElement = $domDocument->getElementsByTagName('span')->item(0);

        $renderClass = $this->phRender->getConfig('render.class');
        $renderAttr = $this->phRender->getConfig('render.attr');

        $this->repeat->render($domElement, $this->scope);
        $renderedHtml = DOMUtils::saveHtml($domDocument);

        $expectedHtml = '<span ng-repeat="n in bar" class="ng-hide">{{' . $expression . '}}</span>';

        for($i = 0; $i < 6; $i++) {
            $expectedHtml .= '<span class="' . $renderClass . '"><span '
                . $renderAttr . '="' . $expression . '">' . $expectedArray[$i] . '</span></span>';
        }
        

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function repeatSpectialPropertiesProvider() {
        return array(
            array('n', array('1', '2', '3', '4', '5', '6')),
            array('$index', array('0', '1', '2', '3', '4', '5')),
            array('$first', array('true', 'false', 'false', 'false', 'false', 'false')),
            array('$middle', array('false', 'true', 'true', 'true', 'true', 'false')),
            array('$last', array('false', 'false', 'false', 'false', 'false', 'true')),
            array('$even', array('true', 'false', 'true', 'false', 'true', 'false')),
            array('$odd', array('true', 'false', 'true', 'false', 'true', 'false')),
        );
    }
}