<?php
/*
 * This file is part of the phCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Tests\Template\Directive;

use PhCompile\PhCompile,
    PhCompile\Scope,
    PhCompile\Template\Directive\NgClass,
    PhCompile\DOM\Utils;

class NgClassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhCompile
     */
    protected $phCompile;
    /**
     * @var NgClass
     */
    protected $class;
    /**
     * @var Scope
     */
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->class = new NgClass($this->phCompile);
        $this->scope = new Scope();
    }


    /**
     * @covers PhCompile\Template\Directive\NgClass::compile
     * @covers PhCompile\Template\Directive\NgClass::compileString
     * @covers PhCompile\Template\Directive\NgClass::compileArray
     * @covers PhCompile\Template\Directive\NgClass::compileObject
     * @dataProvider compileProvider
     */
    public function testCompile($scopeData, $classString, $expected) {
        $this->scope->setData($scopeData);

        $document = Utils::loadHTML('<span ng-class="' . $classString . '"></span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $compiledHtml = Utils::saveHTML($this->class->compile($element, $this->scope)->ownerDocument);
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
     * @expectedException PhCompile\Template\Expression\InvalidExpressionException
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