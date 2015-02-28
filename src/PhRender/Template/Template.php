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
    PhRender\Scope,
    PhRender\Template\Expression,
    PhRender\DOM\RecursiveDOMIterator;

/**
 * Represents HTML template and contains all it's data.
 */
class Template {

    /**
     * PhRender reference for internal use.
     *
     * @var PhRender
     */
    protected $phRender;

    /**
     * Templates path if template was loaded from file.
     *
     * @var string
     */
    protected $path = null;

    /**
     * HTML content of the template.
     *
     * @var string
     */
    protected $html = '';

    /**
     * Scope object with data for certain template.
     *
     * @var Scope
     */
    protected $scope;

    public function __construct(PhRender $phRender) {
        $this->phRender = $phRender;
        $this->scope = new Scope();
        
    }

    /**
     * Sets Scope with data for this template.
     *
     * @param Scope $scope Scope object with template data.
     */
    public function setScope(Scope $scope) {
        $this->scope = $scope;
    }

    /**
     * Returns template's Scope object.
     *
     * @return Scope Template Scope object.
     */
    public function getScope() {
        return $this->scope;
    }

    /**
     * Loads template HTML from file.
     *
     * @param type $path Path to template file.
     * @throws TemplateNotFoundException Throw's exception if file does not exist.
     */
    public function loadHtml($path) {
        if(file_exists($path) === false) {
            throw new TemplateNotFoundException(
                sprintf(
                    'Template file: "%s" does not exist!',
                    $path
                )
            );
        }

        $this->html = file_get_contents($path);
        $this->path = $path;
    }

    /**
     * Sets template HTML.
     *
     * @param string $html New template HTML.
     */
    public function setHtml($html) {
        $this->html = $html;
    }

    /**
     * Returns template HTML.
     *
     * @return string Template HTML.
     */
    public function getHtml() {
        return $this->html;
    }

    /**
     * Renders template HTML.
     * This method uses registered renderers and expressions to render AngularJS
     * template with given Scope data.
     *
     * @param bool $decodeEntities Indicates if method sould decode all HTML entities
     * e.g. &amp; back before returning rendered HTML.
     * @return string Rendered HTML.
     */
    public function render($decodeEntities = true) {
        $domDocument = new \DOMDocument();
        @$domDocument->loadHTML($this->html);

        $domIterator = new \RecursiveIteratorIterator(
                    new RecursiveDOMIterator($domDocument),
                    \RecursiveIteratorIterator::SELF_FIRST);

        foreach($domIterator as $domNode) {
            if($domNode->nodeType === XML_ELEMENT_NODE) {
                $this->renderAttributes($domNode);
            }
        }
        
        $this->html = \PhRender\DOM\DOMUtils::saveHtml($domDocument);
        if($decodeEntities === true) {
            $this->html = html_entity_decode($this->html);
        }
        $this->renderExpressions();

        return $this->html;
    }

    /**
     * Renders DOM elements using registered attribute renderers.
     *
     * @see PhRender::registerAttributeRenderer.
     * @param \DomElement $domElement DOM element which attributes to render.
     */
    protected function renderAttributes(\DomElement $domElement) {
        foreach($domElement->attributes as $attribute) {
            $renderer = $this->phRender->getAttributeRenderer($attribute->name);
            if($renderer !== null) {
                $renderer->render($domElement, $this->scope);
            }
        }
    }

    /**
     * Renders DOM elements using registered element renderers.
     *
     * @todo Implement this method.
     * @todo Write tests.
     *
     * @param \DomElement $domElement DOM element to render.
     */
    protected function renderElement(\DomElement $domElement) {
    }

    protected function renderExpressions() {
        $foundExpressions = array();
        $renderAttribute = $this->phRender->getConfig('render.attr');

        preg_match_all('/{{([^}]+)}}/', $this->html, $foundExpressions);
        foreach($foundExpressions[1] as $foundExpression) {
            $expression = new Expression($this->phRender);
            
            $renderedExpression = $expression->render($foundExpression, $this->scope);
            $renderedExpression = '<span ' . $renderAttribute . '="' . $foundExpression . '">' . $renderedExpression . '</span>';

            $this->html = str_replace('{{' . $foundExpression . '}}',
                $renderedExpression, $this->html);
        }
    }
    
}