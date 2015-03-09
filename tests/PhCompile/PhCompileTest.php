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
     * @covers PhCompile\PhCompile::setDefaultConfig
     */
    public function testSetDefaultConfig()
    {
        $this->assertEquals(array(
            'compile' => array(
                'class' => 'ng-phcompile',
                'attr' => 'ng-phcompile'
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
     * @depends testGetDirectives
     */
    public function testAddDefaultDirectives()
    {
        $directives = $this->phCompile->getDirectives();

        $this->assertNotEmpty($directives);
    }
}