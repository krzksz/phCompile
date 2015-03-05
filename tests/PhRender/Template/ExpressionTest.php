<?php
/*
 * This file is part of the ngPhRender package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile;

use PhRender\Template\Expression;

class ExpressionTest extends \PHPUnit_Framework_TestCase
{
    protected $phRender;
    protected $scope;

    public function setUp() {
        $this->phRender = new PhCompile();
        $this->scope = new Scope();
    }

    /**
     * @covers PhRender\Template\Expression::render
     * @dataProvider renderReplaceProvider
     */
    public function testRenderReplace($scopeData, $expressionString, $expected) {
        $this->scope->setData($scopeData);
        $expression = new Expression($this->phRender);
        $this->assertSame($expected, $expression->compile($expressionString, $this->scope));
    }

    public function renderReplaceProvider() {
        return array(
            array(
                array('foo' => 'bar'), 'foo', 'bar'
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo[1]', 'baz'
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo[0]', 'bar'
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo[99]', null
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo.baz', null
            ),
            array(
                array('foo' => array('bar' => 'baz')), 'foo.bar', 'baz'
            ),
            array(
                array('foo' => array('bar' => 'baz')), '{foo.bar}', 'baz'
            ),
            array(
                array('foo' => array('bar' => 'baz')), '{{ foo.bar }}', 'baz'
            )
        );
    }

    /**
     * @covers PhRender\Template\Expression::render
     * @dataProvider renderEvaluateProvider
     */
    public function testRenderEvaluate($scopeData, $expressionString, $expected) {
        $this->scope->setData($scopeData);
        $expression = new Expression($this->phRender);
        $this->assertEquals($expected, $expression->compile($expressionString, $this->scope));
    }

    public function renderEvaluateProvider() {
        return array(
            array(
                array(), '1+2', '3'
            ),
            array(
                array(), '4/2', '2'
            ),
            array(
                array(), '4+6>0', true
            ),
            array(
                array(), '2+2===5', false
            ),
            array(
                array(), 'foo+bar>0', false
            ),
            array(
                array(), 'foo+bar==0', true
            ),
            array(
                array(), 'foo+bar<1', true
            ),
            array(
                array('foo' => 1, 'bar' => 2), 'foo+bar', '3'
            ),
            array(
                array('foo' => 1, 'bar' => 2), 'foo-bar', '-1'
            ),
            array(
                array('foo' => 'bar', 'boo' => 'bee'), 'foo == \'bar\'', true
            )
        );
    }

    /**
     * @expectedException PhRender\Template\InvalidExpressionException
     */
    public function testForbidFunction() {
        $this->scope->setData(array());
        $expression = new Expression($this->phRender);
        $expression->compile('system()', $this->scope);
    }
}