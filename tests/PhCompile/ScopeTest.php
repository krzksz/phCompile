<?php
/*
 * This file is part of the ngPhCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Tests;

use PhCompile\Scope;

class ScopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Scope
     */
    protected $scope;

    protected function setUp()
    {
        $this->scope = new Scope;
    }

    /**
     * @covers PhCompile\Scope::setData
     * @covers PhCompile\Scope::getData
     * @dataProvider setAndGetDataProvider
     */
    public function testSetAndGetData($data, $accessString, $expected)
    {
        $this->scope->setData($data);
        $this->assertSame($data, $this->scope->getData());
        $this->assertSame($expected, $this->scope->getData($accessString));
    }

    public function setAndGetDataProvider()
    {
        return array(
            array(
                array('foo' => 'bar'), 'foo', 'bar'
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo[1]', 'baz'
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo[0]', 'bar'
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo[99]', null
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo.baz', null
            ),
            array(
                array('foo' => array('bar' => 'baz')), 'foo.bar', 'baz'
            ),
            array(
                array('foo' => array('bar' => 'baz')), 'foo[\'bar\']', 'baz'
            )
        );
    }

    /**
     * @covers PhCompile\Scope::hasData
     * @dataProvider hasDataProvider
     * @depends testSetAndGetData
     */
    public function testHasData($data, $accessString, $expected)
    {
        $this->scope->setData($data);
        $this->assertSame($expected, $this->scope->hasData($accessString));
    }

    public function hasDataProvider()
    {
        return array(
            array(
                array('foo' => 'bar'), 'foo', true
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo[1]', true
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo[0]', true
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo[99]', false
            ),
            array(
                array('foo' => array('bar', 'baz')), 'foo.baz', false
            ),
            array(
                array('foo' => array('bar' => 'baz')), 'foo.bar', true
            ),
            array(
                array('foo' => array('bar' => 'baz')), 'foo[\'bar\']', true
            )
        );
    }
}