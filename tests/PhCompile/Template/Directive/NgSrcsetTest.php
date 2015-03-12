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
    PhCompile\Template\Directive\NgSrcset,
    PhCompile\DOM\Utils;

class NgSrcsetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhCompile
     */
    protected $phCompile;
    /**
     * @var NgSrcset
     */
    protected $ngSrcset;
    /**
     * @var Scope
     */
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->ngSrcset = new NgSrcset($this->phCompile);
        $this->scope = new Scope();
    }


    /**
     * @covers PhCompile\Template\Directive\NgSrcset::compile
     * @dataProvider compileProvider
     */
    public function testCompile($scopeData, $srcString, $expected) {
        $this->scope->setData($scopeData);

        $document = Utils::loadHTML('<span ng-srcset="' . $srcString . '"></span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $compiledHtml = Utils::saveHTML($this->ngSrcset->compile($element, $this->scope)->ownerDocument);
        $expectedHtml = '<span ng-srcset="' . $srcString . '" srcset="' . $expected . '"></span>';

        $this->assertSame($expectedHtml, $compiledHtml);
    }

    public function compileProvider() {
        return array(
            array(
                array('foo' => 'bar'), '{{foo}}', 'bar'
            ),
            array(
                array('foo' => array('bar', 'baz')), 'http://example.com/{{foo[0]}}/{{foo[1]}} x2', 'http://example.com/bar/baz x2'
            ),
            array(
                array('foo' => 'bar', 'baz' => 'zaz'), 'http://example.com/{{foo}}/{{baz}} x2', 'http://example.com/bar/zaz x2'
            ),
        );
    }

}