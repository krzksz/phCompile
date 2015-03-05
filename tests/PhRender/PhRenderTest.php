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

use PhRender\Template\Renderer\NgRepeat;

class PhRenderTest extends \PHPUnit_Framework_TestCase
{

    protected $phRender;

    protected function setUp()
    {
        $this->phRender = new PhCompile;
    }

    /**
     * @covers PhRender\PhRender::setConfig
     * @covers PhRender\PhRender::getConfig
     */
    public function testSetAndGetConfig()
    {
        $this->phRender->setConfig(array('foo' => array('bar' => 'baz')));
        $this->assertEquals('baz', $this->phRender->getConfig('foo.bar'));
    }

    /**
     * @covers PhRender\PhRender::registerAttributeRenderer
     * @covers PhRender\PhRender::getAttributeRenderer
     */
    public function testRegisterAndGetAttributeRenderer()
    {
        $repeat = new NgRepeat($this->phRender);
        $this->phRender->registerAttributeDirective('foo', $repeat);
        $this->assertSame($repeat, $this->phRender->getAttributeParser('foo'));
    }

}
