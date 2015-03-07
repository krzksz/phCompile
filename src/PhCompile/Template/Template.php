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
    PhCompile\DOM\RecursiveDOMIterator,
    PhCompile\Template\Directive\Directive;

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
    public function loadHTML($path)
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
    public function setHTML($html)
    {
        $this->html = $html;
    }

    /**
     * Returns template HTML.
     *
     * @return string Template HTML string.
     */
    public function getHTML()
    {
        return $this->html;
    }

    /**
     * Compiles template HTML.
     * This method uses registered directives and expressions to render AngularJS
     * template with given scope data.
     *
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
                $this->compileNode($domNode);
            }
        }
        /**
         * Update template HTML from compiled DOM.
         */
        $this->html = Utils::saveHTML($document);

        $this->compileExpressions();

        return $this->html;
    }

    /**
     * Compiles given DOM element with directives added to main PhCompile object.
     *
     * @param \DOMElement $element Dom element to compile.
     * @return boolean Returns false if compilation was interrupted, true otherwise.
     */
    protected function compileNode(\DOMElement $element) {
        $directives = $this->phCompile->getDirectives();
        $interrupt = false;
        foreach($directives as $directive) {
            if($directive->isRestrict('E') === true) {
                $interrupt = $this->compileElement($element, $directive);
            }
            if($interrupt === false && $directive->isRestrict('A') === true) {
                $interrupt = $this->compileAttribute($element, $directive);
            }
            if($interrupt === false && $directive->isRestrict('C') === true) {
                $interrupt = $this->compileClass($element, $directive);
            }
            if($interrupt === true) {
                return false;
            }
        }

        return true;
    }

    /**
     * Compiles given DOM element with directive restricted to elements.
     *
     * @param \DOMElement $element DOM element to compile.
     * @param Directive $directive Directive with elements restriction('E').
     * @return boolean Returns false if compilation should stop, true otherwise.
     */
    protected function compileElement(\DOMElement $element, Directive $directive) {
        $directiveName = $directive->getName();
        if($element->tagName === $directiveName) {
            $directive->compile($element, $this->getScope());
        }

        return $directive->doesInterrupt();
    }

    /**
     * Compiles given DOM element with directive restricted to attributes.
     *
     * @param \DOMElement $element DOM element to compile.
     * @param Directive $directive Directive with attributes restriction('A').
     * @return boolean Returns false if compilation should stop, true otherwise.
     */
    protected function compileAttribute(\DOMElement $element, Directive $directive) {
        $directiveName = $directive->getName();
        if($element->hasAttribute($directiveName) === true) {
            $directive->compile($element, $this->getScope());
        }

        return $directive->doesInterrupt();
    }

    /**
     * Compiles given DOM element with directive restricted to classes.
     *
     * @param \DOMElement $element DOM element to compile.
     * @param Directive $directive Directive with classes restriction('C').
     * @return boolean Returns false if compilation should stop, true otherwise.
     */
    protected function compileClass(\DOMElement $element, Directive $directive) {
        $directiveName = $directive->getName();
        if(Utils::hasClass($element, $directiveName) === true) {
            $directive->compile($element, $this->getScope());
        }

        return $directive->doesInterrupt();
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