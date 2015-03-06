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
    PhCompile\Template\Expression\Expression,
    PhCompile\DOM\Utils,
    PhCompile\DOM\RecursiveDOMIterator;

/**
 * Represents HTML template and contains all it's data.
 */
class Template
{
    /**
     * PhCompile reference for internal use.
     *
     * @var PhCompile
     */
    protected $phCompile;

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

    /**
     * Creates new Template object.
     * Templates are used as containers and compiling managers for given HTML.
     *
     * @param PhCompile $phCompile PhCompile object.
     */
    public function __construct(PhCompile $phCompile)
    {
        $this->phCompile = $phCompile;
        $this->scope     = new Scope();
    }

    /**
     * Sets Scope with data for this template.
     *
     * @param Scope $scope Scope object with template data.
     */
    public function setScope(Scope $scope)
    {
        $this->scope = $scope;
    }

    /**
     * Returns template's Scope object.
     *
     * @return Scope Template Scope object.
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Loads template HTML from file.
     *
     * @param string $path Path to template file.
     * @throws TemplateNotFoundException Throw's exception if file does not exist.
     */
    public function loadHtml($path)
    {
        $html = file_get_contents($path);

        if ($html === false) {
            throw new TemplateNotFoundException(
                sprintf(
                    'Template file: "%s" does not exist!', $path
                )
            );
        }
        $this->html = $html;
        $this->path = $path;
    }

    /**
     * Sets template HTML.
     *
     * @param string $html Template HTML string.
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    /**
     * Returns template HTML.
     *
     * @return string Template HTML string.
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Compiles template HTML.
     * This method uses registered compilers and expressions to render AngularJS
     * template with given Scope data.
     *
     * @param bool $decodeHTMLEntities Indicates if method sould decode all HTML entities
     * e.g. &amp; back before returning rendered HTML.
     * @return string Compiled HTML.
     */
    public function compile()
    {
        $document = Utils::loadHTML($this->html);

        $domIterator = new \RecursiveIteratorIterator(
            new RecursiveDOMIterator($document),
            \RecursiveIteratorIterator::SELF_FIRST);

        /**
         * Iterate over every element inside DOM.
         */
        foreach ($domIterator as $domNode) {
            if ($domNode->nodeType === XML_ELEMENT_NODE) {
                if ($this->compileAttributes($domNode) === false) {
                    break;
                }
            }
        }

        /**
         * Update template HTML from compiled DOM.
         */
        $this->html = Utils::saveHTML($document);

        $this->compileExpressions();

        return $this->html;
    }

    protected function compileElement(\DOMElement $element) {
        
    }

    /**
     * Compiles DOM elements using registered attribute directives.
     *
     * @see PhCompile::registerAttributeDirective.
     * @param \DomElement $domElement DOM element which attributes to compile.
     */
    protected function compileAttributes(\DomElement $domElement)
    {
        foreach ($domElement->attributes as $attribute) {
            $directive = $this->phCompile->getAttributeDirective($attribute->name);
            if ($directive !== null) {
                $directive->compile($domElement, $this->scope);

                if ($directive->haltCompiling() === true) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Finds and compiles expressions in templates HTML.
     *
     * @throws InvalidExpressionException Throws exception if function call is found inside expression.
     */
    protected function compileExpressions()
    {
        $foundExpressions = array();
        $renderAttribute  = $this->phCompile->getConfig('compile.attr');
        $expression       = new Expression($this->phCompile);

        /**
         * Find all {{}} expressions.
         */
        preg_match_all('/{{([^}]+)}}/', $this->html, $foundExpressions);
        foreach ($foundExpressions[1] as $foundExpression) {
            /**
             * Render and cover with span for easy client-site reverting.
             */
            $renderedExpression = $expression->compile($foundExpression,
                $this->scope);
            $renderedExpression = '<span '.$renderAttribute.'="'.$foundExpression.'">'.$renderedExpression.'</span>';

            /**
             * Replace {{}} expression with rendered value.
             */
            $this->html = str_replace('{{'.$foundExpression.'}}',
                $renderedExpression, $this->html);
        }
    }
}