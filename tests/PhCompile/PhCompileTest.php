<?php
/*
 * This file is part of the ngPhCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile;

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
}