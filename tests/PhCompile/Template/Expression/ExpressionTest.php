<?php
/*
 * This file is part of the ngPhCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Tests\Template\Expression;

use PhCompile\PhCompile,
    PhCompile\Scope,
    PhCompile\Template\Expression\Expression;

class ExpressionTest extends \PHPUnit_Framework_TestCase
{
    protected $phCompile;
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->scope = new Scope();
    }

    /**
     * @covers PhCompile\Template\Expression\Expression::compile
     * @dataProvider renderCompileProvider
     */
    public function testCompileReplace($scopeData, $expressionString, $expected) {
        $this->scope->setData($scopeData);
        $expression = new Expression($this->phCompile);
        $this->assertSame($expected, $expression->compile($expressionString, $this->scope));
    }

    public function renderCompileProvider() {
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
     * @covers PhCompile\Template\Expression\Expression::compile
     * @dataProvider compileEvaluateProvider
     */
    public function testCompileEvaluate($scopeData, $expressionString, $expected) {
        $this->scope->setData($scopeData);
        $expression = new Expression($this->phCompile);
        $this->assertEquals($expected, $expression->compile($expressionString, $this->scope));
    }

    public function compileEvaluateProvider() {
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
     * @expectedException PhCompile\Template\Expression\InvalidExpressionException
     */
    public function testForbidFunction() {
        $this->scope->setData(array());
        $expression = new Expression($this->phCompile);
        $expression->compile('system()', $this->scope);
    }
}