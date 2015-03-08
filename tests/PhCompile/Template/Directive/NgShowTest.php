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
    PhCompile\DOM\Utils;
use PhCompile\Template\Directive\NgShow;

class NgShowTest extends \PHPUnit_Framework_TestCase
{
    protected $phCompile;
    protected $ngShow;
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->ngShow = new NgShow($this->phCompile);
        $this->scope = new Scope();
    }


    /**
     * @covers PhCompile\Template\Directive\NgShow::compile
     * @dataProvider compileProvider
     */
    public function testCompile($scopeData, $expression, $expectedClass) {
        $this->scope->setData($scopeData);

        $document = Utils::loadHTML('<span ng-show="' . $expression . '" class=""></span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $this->ngShow->compile($element, $this->scope);

        $renderedHtml = Utils::saveHTML($element->ownerDocument);
        $expectedHtml = '<span ng-show="' . $expression . '" class="' . $expectedClass .'"></span>';

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function compileProvider() {
        return array(
            array(
                array('foo' => true),
                'foo',
                ''
            ),
            array(
                array('foo' => false),
                'foo',
                'ng-hide'
            ),
            array(
                array('foo' => false),
                'bar',
                'ng-hide'
            ),
            array(
                array('foo' => ''),
                'foo',
                'ng-hide'
            )
        );
    }
}