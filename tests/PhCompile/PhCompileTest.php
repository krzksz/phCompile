<?php
/*
 * This file is part of the phCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Tests;

use PhCompile\PhCompile,
    PhCompile\Template\Directive\NgRepeat;
use PhCompile\Scope;

class PhCompileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhCompile
     */
    protected $phCompile;

    protected function setUp()
    {
        $this->phCompile = new PhCompile;
    }

    /**
     * @covers PhCompile\PhCompile::setConfig
     * @covers PhCompile\PhCompile::getConfig
     */
    public function testSetAndGetConfig()
    {
        $config = array('foo' => array('bar' => 'baz'));
        $this->phCompile->setConfig($config);
        $this->assertEquals('baz', $this->phCompile->getConfig('foo.bar'));
    }

    /**
     * @covers PhCompile\PhCompile::__construct
     * @depends testSetAndGetConfig
     */
    public function testSetConfigConstruct() {
        $config = array('foo' => array('bar' => 'baz'));
        $phCompile = new PhCompile($config);

        $this->assertEquals('baz', $phCompile->getConfig('foo.bar'));
    }

    /**
     * @covers PhCompile\PhCompile::setDefaultConfig
     */
    public function testSetDefaultConfig()
    {
        $this->assertEquals(array(
            'compile' => array(
                'class' => 'ng-phcompile',
                'attr' => 'ng-phcompile'
            ),
            'directive' => array(
                'defaults'  =>  true
            )
        ), $this->phCompile->getConfig());
    }

    /**
     * @covers PhCompile\PhCompile::addDirective
     */
    public function testAddDirective()
    {
        $repeat = new NgRepeat($this->phCompile);
        $this->phCompile->addDirective($repeat);

        $this->assertAttributeContains($repeat, 'directives', $this->phCompile);
    }

    /**
     * @covers PhCompile\PhCompile::addDirective
     * @expectedException \InvalidArgumentException
     */
    public function testAddDirectiveWithoutName()
    {
        $directiveStub = $this->getMockForAbstractClass('PhCompile\Template\Directive\Directive',
            array(new PhCompile));
        $this->phCompile->addDirective($directiveStub);
    }

    /**
     * @covers PhCompile\PhCompile::getDirectives
     * @depends testAddDirective
     */
    public function testGetDirectives()
    {
        $repeat     = new NgRepeat($this->phCompile);
        $this->phCompile->addDirective($repeat);
        $directives = $this->phCompile->getDirectives();

        $this->assertContains($repeat, $directives);
    }

    /**
     * @covers PhCompile\PhCompile::addDefaultDirectives
     * @covers PhCompile\PhCompile::__construct
     * @depends testGetDirectives
     */
    public function testAddDefaultDirectives()
    {
        $this->assertNotEmpty($this->phCompile->getDirectives());
    }

    /**
     * @covers PhCompile\PhCompile::addDefaultDirectives
     * @covers PhCompile\PhCompile::__construct
     * @depends testGetDirectives
     */
    public function testNoDefaultDirectives() {
        $config = array(
            'directive' =>  array(
                'defaults'  => false
            )
        );
        $phCompile = new PhCompile($config);

        $this->assertEmpty($phCompile->getDirectives());
    }
}