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
    ReflectionMethod,
    PhCompile\Scope,
    DOMElement;

class DirectiveTest extends \PHPUnit_Framework_TestCase
{

    protected $directiveStub;

    public function setUp()
    {
        $this->directiveStub = $this->getMockForAbstractClass('PhCompile\Template\Directive\Directive',
            array(new PhCompile));
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::__construct
     */
    public function testRestrictDefault()
    {
        $this->assertAttributeSame('ACE', 'restrict', $this->directiveStub);
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::setName
     */
    public function testSetName()
    {
        $name   = 'foo';
        $method = new ReflectionMethod(
            get_class($this->directiveStub), 'setName'
        );
        $method->setAccessible(true);
        $method->invoke($this->directiveStub, $name);

        $this->assertAttributeSame($name, 'name', $this->directiveStub);
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::getName
     * @depends testSetName
     * @expectedException \InvalidArgumentException
     */
    public function testSetNameNotString()
    {
        $method = new ReflectionMethod(
            get_class($this->directiveStub), 'setName'
        );
        $method->setAccessible(true);
        $method->invoke($this->directiveStub, array());
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::getName
     */
    public function testGetNameDefault()
    {
        $this->assertAttributeSame(null, 'name', $this->directiveStub);
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::getName
     * @depends testSetName
     */
    public function testGetName()
    {
        $name   = 'foo';
        $method = new ReflectionMethod(
            get_class($this->directiveStub), 'setName'
        );
        $method->setAccessible(true);
        $method->invoke($this->directiveStub, $name);

        $this->assertSame($name, $this->directiveStub->getName());
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::setRestrict
     */
    public function testSetRestrict()
    {
        $restrict = 'AE';
        $this->directiveStub->setRestrict($restrict);

        $this->assertAttributeSame($restrict, 'restrict', $this->directiveStub);
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::setRestrict
     */
    public function testSetRestrictUppercase()
    {
        $restrict = 'ae';
        $this->directiveStub->setRestrict($restrict);

        $this->assertAttributeSame(strtoupper($restrict), 'restrict',
            $this->directiveStub);
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::setRestrict
     * @depends testSetRestrict
     * @expectedException \InvalidArgumentException
     */
    public function testSetRestrictNotString()
    {
        $this->directiveStub->setRestrict(array());
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::getRestrict
     * @depends testSetRestrict
     */
    public function testGetRestrict()
    {
        $restrict = 'AE';
        $this->directiveStub->setRestrict($restrict);

        $this->assertSame($restrict, $this->directiveStub->getRestrict());
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::getRestrict
     * @depends testSetRestrict
     * @dataProvider isRestrictProvider
     */
    public function testIsRestict($restrict, $isRestrict, $expected)
    {
        $this->directiveStub->setRestrict($restrict);

        $this->assertSame($expected,
            $this->directiveStub->isRestrict($isRestrict));
    }

    public function isRestrictProvider()
    {
        return array(
            array('AEC', 'A', true),
            array('AEC', 'E', true),
            array('AEC', 'C', true),
            array('A', 'C', false),
            array('AE', 'C', false),
            array('AE', 'A', true),
            array('A', 'A', true),
        );
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::isRestrict
     * @depends testSetRestrict
     * @expectedException \InvalidArgumentException
     */
    public function testIsRestrictNotString()
    {
        $this->directiveStub->setRestrict(array());
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::isRestrict
     * @depends testSetRestrict
     * @expectedException \InvalidArgumentException
     */
    public function testIsRestrictNotSingle()
    {
        $this->directiveStub->isRestrict('AEC');
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::setPriority
     */
    public function testSetPriority()
    {
        $priority = 5.5;
        $this->directiveStub->setPriority($priority);

        $this->assertAttributeSame($priority, 'priority', $this->directiveStub);
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::setPriority
     * @depends testSetRestrict
     * @expectedException \InvalidArgumentException
     */
    public function testSetPriorityNotNumber()
    {
        $this->directiveStub->setPriority(array());
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::getPriority
     * @depends testSetPriority
     */
    public function testGetPriority()
    {
        $priority = 5.5;
        $this->directiveStub->setPriority($priority);

        $this->assertSame($priority, $this->directiveStub->getPriority());
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::getPriority
     * @depends testSetPriority
     */
    public function testGetPriorityDefault()
    {
        $this->assertSame(0, $this->directiveStub->getPriority());
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::compile
     */
    public function testCompile()
    {
        $element = new DOMElement('foo');
        $scope   = new Scope();
        $this->assertInstanceOf('\DOMElement',
            $this->directiveStub->compile($element, $scope));
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::setInterrupt
     */
    public function testSetInterrupt()
    {
        $interrupt = true;
        $this->directiveStub->setInterrupt($interrupt);

        $this->assertAttributeSame($interrupt, 'interrupt', $this->directiveStub);
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::doesInterrupt
     * @depends testSetInterrupt
     */
    public function testDoesInterrupt()
    {
        $interrupt = true;
        $this->directiveStub->setInterrupt($interrupt);

        $this->assertSame($interrupt, $this->directiveStub->doesInterrupt());
    }

    /**
     * @covers PhCompile\Template\Directive\Directive::doesInterrupt
     * @depends testSetInterrupt
     */
    public function testDoesInterruptDefault()
    {
        $this->assertSame(false, $this->directiveStub->doesInterrupt());
    }
}