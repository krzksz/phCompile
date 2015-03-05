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

use PhCompile\Template\Directive\NgRepeat;

class PhCompileTest extends \PHPUnit_Framework_TestCase
{

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
     * @covers PhCompile\PhCompile::setConfig
     * @covers PhCompile\PhCompile::getConfig
     */
    public function testSetDefaultConfig()
    {
        $this->assertEquals(array(
                'compile' => array(
                    'class' => 'ng-phrender',
                    'attr' => 'ng-phrender'
                )
            ), $this->phCompile->getConfig());
    }

    /**
     * @covers PhCompile\PhCompile::registerAttributeDirective
     * @covers PhCompile\PhCompile::getAttributeDirective
     */
    public function testRegisterAndGetAttributeRenderer()
    {
        $repeat = new NgRepeat($this->phCompile);
        $this->phCompile->registerAttributeDirective('foo', $repeat);
        $this->assertSame($repeat, $this->phCompile->getAttributeDirective('foo'));
    }

}
