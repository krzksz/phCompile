<?php
/*
 * This file is part of the ngPhCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Template;

use PhCompile\PhCompile,
    PhCompile\Scope,
    PhCompile\DOM\DOMUtils;

class TemplateTest extends \PHPUnit_Framework_TestCase
{

    protected $template;

    protected function setUp()
    {
        $this->template = new Template(new PhCompile);
    }

    /**
     * @covers PhCompile\Template\Template::setScope
     * @covers PhCompile\Template\Template::getScope
     */
    public function testSetAndGetScope()
    {
        $scope = new Scope();
        $this->template->setScope($scope);
        $this->assertSame($scope, $this->template->getScope());
    }

    /**
     * @covers PhCompile\Template\Template::setHtml
     * @covers PhCompile\Template\Template::getHtml
     */
    public function testSetAndGetHtml()
    {
        $html = file_get_contents(TEST_PATH . 'template/overall.html');
        $this->template->setHtml($html);
        $this->assertSame($html, $this->template->getHtml());
    }

    /**
     * @covers PhCompile\Template\Template::loadHtml
     * @depends testSetAndGetHtml
     */
    public function testLoadHtml()
    {
        $templatePath = TEST_PATH . 'template/overall.html';
        $this->template->loadHtml($templatePath);
        $this->assertSame(file_get_contents($templatePath), $this->template->getHtml());
    }

    /**
     * @covers PhCompile\Template\Template::compile
     */
    public function testCompile()
    {
        $this->template->loadHtml(TEST_PATH . 'template/overall.html');
        $scopeData = json_decode(file_get_contents(TEST_PATH . 'template/overallData.json'), true);
        $this->template->getScope()->setData($scopeData);
        $domDocument = new \DOMDocument();

        @$domDocument->loadHTML(mb_convert_encoding(file_get_contents(TEST_PATH . 'template/overallRendered.html'), 'HTML-ENTITIES', 'UTF-8'));

        $this->assertEquals(DOMUtils::saveHtml($domDocument),
            $this->template->compile());
    }
}
