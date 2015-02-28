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

use PhRender\Template\Renderer\NgBind,
    PhRender\DOM\DOMUtils;

class BindTest extends \PHPUnit_Framework_TestCase
{
    protected $phRender;
    protected $bind;
    protected $scope;

    public function setUp() {
        $this->phRender = new PhRender();
        $this->bind = new NgBind($this->phRender);
        $this->scope = new Scope();
    }


    /**
     * @covers PhRender\Template\Renderer\NgBind::render
     * @dataProvider renderProvider
     */
    public function testRender($scopeData, $bindString, $expected) {
        $this->scope->setData($scopeData);
        
        $domDocument = new \DOMDocument();
        $domDocument->loadHTML('<span ng-bind="' . $bindString . '"></span>');
        $domElement = $domDocument->getElementsByTagName('span')->item(0);
        
        $renderedHtml = DOMUtils::saveHtml($this->bind->render($domElement, $this->scope)->ownerDocument);
        $expectedHtml = '<span ng-bind="' . $bindString . '">' . $expected . '</span>';
        
        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function renderProvider() {
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