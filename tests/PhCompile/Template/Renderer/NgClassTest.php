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

use PhCompile\Template\Directive\NgClass,
    PhCompile\DOM\Utils;

class NgClassTest extends \PHPUnit_Framework_TestCase
{
    protected $phCompile;
    protected $class;
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->class = new NgClass($this->phCompile);
        $this->scope = new Scope();
    }


    /**
     * @covers PhRender\Template\Directive\NgClass::compile
     * @dataProvider compileProvider
     */
    public function testRender($scopeData, $classString, $expected) {
        $this->scope->setData($scopeData);

        $document = Utils::loadHTML('<span ng-class="' . $classString . '"></span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $compiledHtml = Utils::saveHtml($this->class->compile($element, $this->scope)->ownerDocument);
        $expectedHtml = '<span ng-class="' . $classString . '" class="' . $expected . '"></span>';

        $this->assertSame($expectedHtml, $compiledHtml);
    }

    public function compileProvider() {
        return array(
            array(
                array('foo' => 'bar'), 'foo', 'bar'
            ),
            array(
                array('foo' => 'bar'), 'zaz', ''
            ),
            array(
                array('foo' => 'bar baz'), 'foo', 'bar baz'
            ),
            array(
                array('foo' => ''), 'foo', ''
            ),
            array(
                array('foo' => 'bar', 'baz' => 'zaz'), '[foo, baz]', 'bar zaz'
            ),
            array(
                array('foo' => 'bar', 'baz' => false), '[foo, baz]', 'bar'
            ),
            array(
                array('foo' => true, 'bar' => true), '{bar: foo, zaz: bar}', 'bar zaz'
            ),
            array(
                array('foo' => true, 'bar' => false), '{bar: foo, zaz: bar}', 'bar'
            ),
            array(
                array('foo' => true, 'bar' => 'foo'), '{bar: foo, zaz: bar}', 'bar zaz'
            )
        );
    }

    /**
     * @covers PhCompile\Template\Directive\NgClass::compile
     * @dataProvider compileExceptionProvider
     * @expectedException PhCompile\Template\InvalidExpressionException
     */
    public function testCompileException($scopeData, $classString) {
        $this->scope->setData($scopeData);

        $document = Utils::loadHTML('<span ng-class="' . $classString . '"></span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $this->class->compile($element, $this->scope);
    }

    public function compileExceptionProvider() {
        return array(
            array(
                array('foo' => array('bar', 'baz')), 'foo'
            ),
            array(
                array('foo' => 'bar', 'baz' => array('bar', 'baz')), '[foo, baz]',
            )
        );
    }

}