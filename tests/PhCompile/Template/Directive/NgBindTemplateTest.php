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
    PhCompile\Template\Directive\NgBindTemplate,
    PhCompile\DOM\Utils;

class NgBindTemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhCompile
     */
    protected $phCompile;
    /**
     * @var NgBind
     */
    protected $bindTemplate;
    /**
     * @var Scope
     */
    protected $scope;

    public function setUp() {
        $this->phCompile = new PhCompile();
        $this->bindTemplate = new NgBindTemplate($this->phCompile);
        $this->scope = new Scope();
    }


    /**
     * @covers PhCompile\Template\Directive\NgBindTemplate::compile
     * @dataProvider compileProvider
     */
    public function testCompile($scopeData, $bindString, $expected) {
        $this->scope->setData($scopeData);

        $document = Utils::loadHTML('<span ng-bind-template="' . $bindString . '"></span>');
        $element = $document->getElementsByTagName('span')->item(0);

        $compiledHtml = Utils::saveHTML($this->bindTemplate->compile($element, $this->scope)->ownerDocument);
        $expectedHtml = '<span ng-bind-template="' . $bindString . '">' . $expected . '</span>';

        $this->assertSame($expectedHtml, $compiledHtml);
    }

    public function compileProvider() {
        return array(
            array(
                array('foo' => 'bar'), '{{foo}}', 'bar'
            ),
            array(
                array('foo' => array('bar', 'baz')), '{{foo[0]}} {{foo[1]}}', 'bar baz'
            ),
            array(
                array('foo' => 'bar', 'baz' => 'zaz'), '{{foo}} {{baz}}', 'bar zaz'
            ),
        );
    }

}