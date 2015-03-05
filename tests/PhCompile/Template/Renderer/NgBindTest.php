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

use PhCompile\Template\Directive\NgBind,
    PhCompile\DOM\DOMUtils;

class BindTest extends \PHPUnit_Framework_TestCase
{
    protected $phCompile;
    protected $bind;
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->bind = new NgBind($this->phCompile);
        $this->scope = new Scope();
    }


    /**
     * @covers PhCompile\Template\Directive\NgBind::compile
     * @dataProvider compileProvider
     */
    public function testCompile($scopeData, $bindString, $expected) {
        $this->scope->setData($scopeData);
        
        $domDocument = new \DOMDocument();
        $domDocument->loadHTML('<span ng-bind="' . $bindString . '"></span>');
        $domElement = $domDocument->getElementsByTagName('span')->item(0);
        
        $compiledHtml = DOMUtils::saveHtml($this->bind->compile($domElement, $this->scope)->ownerDocument);
        $expectedHtml = '<span ng-bind="' . $bindString . '">' . $expected . '</span>';
        
        $this->assertSame($expectedHtml, $compiledHtml);
    }

    public function compileProvider() {
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
            )
        );
    }

}