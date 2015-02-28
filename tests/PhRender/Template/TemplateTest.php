<?php
/*
 * This file is part of the ngPhRender package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhRender\Template;

use PhRender\PhRender,
    PhRender\Scope;

class TemplateTest extends \PHPUnit_Framework_TestCase
{

    protected $template;

    protected function setUp()
    {
        $this->template = new Template(new PhRender);
    }

    /**
     * @covers PhRender\Template\Template::setScope
     * @covers PhRender\Template\Template::getScope
     */
    public function testSetAndGetScope()
    {
        $scope = new Scope();
        $this->template->setScope($scope);
        $this->assertSame($scope, $this->template->getScope());
    }

    /**
     * @covers PhRender\Template\Template::setHtml
     * @covers PhRender\Template\Template::getHtml
     */
    public function testSetAndGetHtml()
    {
        $html = file_get_contents(TEST_PATH . 'template/overall.html');
        $this->template->setHtml($html);
        $this->assertSame($html, $this->template->getHtml());
    }

    /**
     * @covers PhRender\Template\Template::loadHtml
     * @depends testSetAndGetHtml
     */
    public function testLoadHtml()
    {
        $templatePath = TEST_PATH . 'template/overall.html';
        $this->template->loadHtml($templatePath);
        $this->assertSame(file_get_contents($templatePath), $this->template->getHtml());
    }

    /**
     * @covers PhRender\Template\Template::render
     */
    public function testRender()
    {
        $this->template->loadHtml(TEST_PATH . 'template/overall.html');
        $scopeData = json_decode(file_get_contents(TEST_PATH . 'template/overallData.json'), true);
        $this->template->getScope()->setData($scopeData);
        $domDocument = new \DOMDocument();
        @$domDocument->loadHTMLFile(TEST_PATH . 'template/overallRendered.html');
        $this->assertEquals(html_entity_decode(\PhRender\DOM\DOMUtils::saveHtml($domDocument)),
            $this->template->render());
    }
}
