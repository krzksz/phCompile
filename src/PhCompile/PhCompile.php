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

use SplPriorityQueue,
    PhCompile\Template\Directive\Directive,
    PhCompile\Template\Directive\NgVisibility,
    PhCompile\Template\Directive\NgRepeat,
    PhCompile\Template\Directive\NgBind,
    PhCompile\Template\Directive\NgClass;

/**
 * Server side compiler for AngularJS templates.
 *
 * This class is responsible for containing configuration,
 * managing compilers and compiling template.
 */
class PhCompile
{
    /**
     * Scope object containing entire configuration.
     *
     * @var Scope
     */
    protected $config = null;

    /**
     * Contains all registered attributes with corresponding compiler
     * objects.
     *
     * @var array
     */
    protected $directives = null;

    /**
     * Creates new PhRender object.
     */
    public function __construct()
    {
        $this->config = new Scope();
        $this->setDefaultConfig();

        $this->directives = new SplPriorityQueue();
        $this->addDefaultDirectives();
    }

    /**
     * Sets default PhRender config that is later used by other library's objects.
     */
    protected function setDefaultConfig()
    {
        $this->config->setData(
            array(
                'compile' => array(
                    /**
                     * Class used to tag server side compiled elements.
                     */
                    'class' => 'ng-phcompile',
                    /**
                     * Attribute used to tag server side compiled expressions.
                     */
                    'attr' => 'ng-phcompile'
                )
            )
        );
    }

    /**
     * Sets config given as array to config Scope object.
     * Existing elements in config's Scope will be overwritten.
     *
     * @param array $config Array containing new config.
     */
    public function setConfig(array $config)
    {
        $this->config->setData($config);
    }

    /**
     * Returns value corresponding to given access string.
     *
     * @param string|null $accessString Access string to wanted config value,
     * null if you want entire config array.
     * @return mixed Value corresponding to given access string or null if value
     * does not exist.
     */
    public function getConfig($accessString = null)
    {
        return $this->config->getData($accessString);
    }

    /**
     * Registers Directive object.
     * Directive's method compile() will be called each time given name is
     * found inside matching restrict DOM part.
     * compile() method is called with entire\ DOMElement.
     *
     * @param string $string String that directive matches.
     * @param Directive $directive New directive object.
     */
    public function addDirective(Directive $directive)
    {
        $this->directives->insert($directive, $directive->getPriority());
    }

    /**
     * Returns entire directives priority queue.
     *
     * @return SplPriorityQueue Derectives queue.
     */
    public function getDirectives() {
        return $this->directives;
    }

    /**
     * Registers default directives.
     */
    protected function addDefaultDirectives()
    {
        $defaultAttributes = array(
            new NgRepeat($this),
            new NgVisibility($this),
            new NgVisibility($this),
            new NgBind($this),
            new NgClass($this)
        );

        foreach ($defaultAttributes as $directive) {
            $this->addDirective($directive);
        }
    }
}