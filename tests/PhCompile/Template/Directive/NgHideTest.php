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
    PhCompile\DOM\Utils,
    PhCompile\Template\Directive\NgHide;

class NgHideTest extends \PHPUnit_Framework_TestCase
{
    protected $phCompile;
    /**
     * @var NgHide
     */
    protected $ngHide;
    /**
     * @var Scope
     */
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->ngHide = new NgHide($this->phCompile);
        $this->scope = new Scope();
    }


    /**
     * @covers PhCompile\Template\Directive\NgHide::compile
     * @dataProvider compileProvider
     */
    public function testCompile($scopeData, $expression, $expectedClass) {
        $this->scope->setData($scopeData);

        $document = Utils::loadHTML('<span ng-hide="' . $expression . '" class=""></span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $this->ngHide->compile($element, $this->scope);

        $renderedHtml = Utils::saveHTML($element->ownerDocument);
        $expectedHtml = '<span ng-hide="' . $expression . '" class="' . $expectedClass .'"></span>';

        $this->assertSame($expectedHtml, $renderedHtml);
    }

    public function compileProvider() {
        return array(
            array(
                array('foo' => true),
                'foo',
                'ng-hide'
            ),
            array(
                array('foo' => false),
                'foo',
                ''
            ),
            array(
                array('foo' => false),
                'bar',
                ''
            ),
            array(
                array('foo' => ''),
                'foo',
                ''
            )
        );
    }
}