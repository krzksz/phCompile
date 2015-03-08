<?php
/*
 * This file is part of the phCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Tests\Template;

use PhCompile\Template\Template,
    PhCompile\PhCompile,
    PhCompile\Scope;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    protected $template;

    protected function setUp()
    {
        $this->template = new Template(new PhCompile);
    }

    /**
     * @covers PhCompile\Template\Template::setScope
     */
    public function testSetScope() {
        $scope = new Scope();
        $this->template->setScope($scope);

        $this->assertAttributeEquals($scope, 'scope', $this->template);
    }

    /**
     * @covers PhCompile\Template\Template::getScope
     * @depends testSetScope
     */
    public function testGetScope() {
        $scope = new Scope();
        $this->template->setScope($scope);

        $this->assertSame($scope, $this->template->getScope());
    }

    /**
     * @covers PhCompile\Template\Template::getScope
     */
    public function testGetDefaultScope() {
        $this->assertInstanceOf('PhCompile\Scope', $this->template->getScope());
    }

    /**
     * @covers PhCompile\Template\Template::setHTML
     */
    public function testSetHTML() {
        $html = file_get_contents(TEST_PATH.'template/overall.html');
        $this->template->setHTML($html);

        $this->assertAttributeSame($html, 'html', $this->template);
    }

    /**
     * @covers PhCompile\Template\Template::getHTML
     * @depends testSetHTML
     */
    public function testGetHTML() {
        $html = file_get_contents(TEST_PATH.'template/overall.html');
        $this->template->setHTML($html);

        $this->assertSame($html, $this->template->getHTML());
    }

    /**
     * @covers PhCompile\Template\Template::loadHTML
     * @depends testGetHTML
     */
    public function testLoadHTML()
    {
        $templatePath = TEST_PATH.'template/overall.html';
        $html = file_get_contents($templatePath);

        $this->template->loadHTML($templatePath);

        $this->assertAttributeSame($html, 'html', $this->template);
        $this->assertSame($html, $this->template->getHTML());
    }

    /**
     * @covers PhCompile\Template\Template::loadHTML
     * @expectedException \InvalidArgumentException
     */
    public function testLoadHTMLNotExisting() {
        $this->template->loadHTML('non-existing-file');
    }
}