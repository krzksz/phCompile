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

use PhCompile\Template\Directive\NgRepeat,
    PhCompile\DOM\Utils;

class RepeatTest extends \PHPUnit_Framework_TestCase
{
    protected $phCompile;
    protected $repeat;
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->repeat = new NgRepeat($this->phCompile);
        $this->scope = new Scope();
    }

    /**
     * @covers PhCompile\Template\Directive\NgRepeat::compile
     */
    public function testRepeatArray() {
        $this->scope->setData(array(
            'bar'   =>  array(
                array(
                    'baz'   =>  'zaz'
                )
            )
        ));
        
        $document = Utils::loadHTML('<span ng-repeat="foo in bar">{{foo.baz}}</span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $renderClass = $this->phCompile->getConfig('compile.class');
        $renderAttr = $this->phCompile->getConfig('compile.attr');

        $this->repeat->compile($element, $this->scope);
        $compiledHtml = Utils::saveHTML($document);
        
        $expectedHtml = '<span ng-repeat="foo in bar" class="ng-hide">{{foo.baz}}</span>';
        $expectedHtml .= '<span class="' . $renderClass . '"><span ' . $renderAttr . '="foo.baz">zaz</span></span>';

        $this->assertSame($expectedHtml, $compiledHtml);
    }

    /**
     * @covers PhCompile\Template\Directive\NgRepeat::compile
     */
    public function testRepeatObject() {
        $this->scope->setData(array(
            'bar'   =>  array(
                'baz'   =>  'zaz'
            )
        ));

        $document = Utils::loadHTML('<span ng-repeat="(key, value) in bar">{{key}}{{value}}</span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $renderClass = $this->phCompile->getConfig('compile.class');
        $renderAttr = $this->phCompile->getConfig('compile.attr');

        $this->repeat->compile($element, $this->scope);
        $comiledHtml = Utils::saveHTML($document);

        $expectedHtml = '<span ng-repeat="(key, value) in bar" class="ng-hide">{{key}}{{value}}</span>';
        $expectedHtml .= '<span class="' . $renderClass . '"><span ' . $renderAttr . '="key">baz</span><span ' . $renderAttr . '="value">zaz</span></span>';

        $this->assertSame($expectedHtml, $comiledHtml);
    }

    /**
     * @covers PhCompile\Template\Directive\NgRepeat::compile
     * @dataProvider repeatSpectialPropertiesProvider
     */
    public function testRepeatSpecialProperties($expression, $expectedArray) {
        $this->scope->setData(array(
            'bar'   =>  array(  1, 2, 3, 4, 5, 6)
        ));

        $document = Utils::loadHTML('<span ng-repeat="n in bar">{{' . $expression . '}}</span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $renderClass = $this->phCompile->getConfig('compile.class');
        $renderAttr = $this->phCompile->getConfig('compile.attr');

        $this->repeat->compile($element, $this->scope);
        $compiledHtml = Utils::saveHTML($document);

        $expectedHtml = '<span ng-repeat="n in bar" class="ng-hide">{{' . $expression . '}}</span>';

        for($i = 0; $i < 6; $i++) {
            $expectedHtml .= '<span class="' . $renderClass . '"><span '
                . $renderAttr . '="' . $expression . '">' . $expectedArray[$i] . '</span></span>';
        }
        

        $this->assertSame($expectedHtml, $compiledHtml);
    }

    public function repeatSpectialPropertiesProvider() {
        return array(
            array('n', array('1', '2', '3', '4', '5', '6')),
            array('$index', array('0', '1', '2', '3', '4', '5')),
            array('$first', array(1, '', '', '', '', '')),
            array('$middle', array('', 1, 1, 1, 1, '')),
            array('$last', array('', '', '', '', '', 1)),
            array('$even', array(1, '', 1, '', 1, '')),
            array('$odd', array('', 1, '', 1, '', 1)),
        );
    }
}