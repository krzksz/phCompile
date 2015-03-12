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
    PhCompile\Template\Directive\NgHref,
    PhCompile\DOM\Utils;

class NgHrefTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhCompile
     */
    protected $phCompile;
    /**
     * @var NgHref
     */
    protected $ngHref;
    /**
     * @var Scope
     */
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->ngHref = new NgHref($this->phCompile);
        $this->scope = new Scope();
    }


    /**
     * @covers PhCompile\Template\Directive\NgHref::compile
     * @dataProvider compileProvider
     */
    public function testCompile($scopeData, $hrefString, $expected) {
        $this->scope->setData($scopeData);

        $document = Utils::loadHTML('<span ng-href="' . $hrefString . '"></span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $compiledHtml = Utils::saveHTML($this->ngHref->compile($element, $this->scope)->ownerDocument);
        $expectedHtml = '<span ng-href="' . $hrefString . '" href="' . $expected . '"></span>';

        $this->assertSame($expectedHtml, $compiledHtml);
    }

    public function compileProvider() {
        return array(
            array(
                array('foo' => 'bar'), '{{foo}}', 'bar'
            ),
            array(
                array('foo' => array('bar', 'baz')), 'http://example.com/{{foo[0]}}/{{foo[1]}}', 'http://example.com/bar/baz'
            ),
            array(
                array('foo' => 'bar', 'baz' => 'zaz'), 'http://example.com/{{foo}}/{{baz}}', 'http://example.com/bar/zaz'
            ),
        );
    }

}