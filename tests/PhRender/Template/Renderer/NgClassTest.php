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

use PhRender\Template\Renderer\NgClass,
    PhRender\DOM\DOMUtils;

class NgClassTest extends \PHPUnit_Framework_TestCase
{
    protected $phRender;
    protected $class;
    protected $scope;

    public function setUp() {
        $this->phRender = new PhRender();
        $this->class = new NgClass($this->phRender);
        $this->scope = new Scope();
    }


    /**
     * @covers PhRender\Template\Renderer\NgClass::render
     * @dataProvider renderProvider
     */
    public function testRender($scopeData, $classString, $expected) {
        $this->scope->setData($scopeData);

        $domDocument = new \DOMDocument();
        $domDocument->loadHTML('<span ng-class="' . $classString . '"></span>');
        $domElement = $domDocument->getElementsByTagName('span')->item(0);

        $renderedHtml = DOMUtils::saveHtml($this->class->render($domElement, $this->scope)->ownerDocument);
        $expectedHtml = '<span ng-class="' . $classString . '" class="' . $expected . '"></span>';

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function renderProvider() {
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
                array('foo' => array('bar', 'baz')), 'foo', 'bar baz'
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
                array('foo' => true, 'bar' => 'foo'), '{bar: foo, zaz: bar}', 'foo bar'
            )
        );
    }

}