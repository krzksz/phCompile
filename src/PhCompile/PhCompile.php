<?php
/*
 * This file is part of the phCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile;

use SplPriorityQueue,
    PhCompile\Template\Directive\Directive,
    PhCompile\Template\Directive\NgShow,
    PhCompile\Template\Directive\NgHide,
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
     * You can pass configuration in shape of an array just like to Scope object.
     *
     * @param array $config Configuration.
     */
    public function __construct(array $config = array())
    {
        /**
         * Set default config and overwrite it with given Scope data.
         */
        $this->config = new Scope();
        $this->setDefaultConfig();
        $this->config->setData($config);


        /**
         * Add default directives if config allows.
         */
        $this->directives = new SplPriorityQueue();
        if ($this->config->getData('directive.defaults') == true) {
            $this->addDefaultDirectives();
        }
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
                ),
                'directive' => array(
                    /**
                     * Tells if PhCompile should add default directives on construct.
                     */
                    'defaults'  =>  true
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
     * @param Directive $directive New directive object.
     */
    public function addDirective(Directive $directive)
    {
        if ($directive->getName() === null) {
            throw new \InvalidArgumentException(sprintf(
                'Directive "%s" does not have a name!', get_class($directive)
            ));
        }

        $this->directives->insert($directive, $directive->getPriority());
    }

    /**
     * Returns entire directives priority queue.
     *
     * @return SplPriorityQueue Derectives queue.
     */
    public function getDirectives()
    {
        return $this->directives;
    }

    /**
     * Registers default directives.
     */
    protected function addDefaultDirectives()
    {
        $defaultAttributes = array(
            new NgRepeat($this),
            new NgShow($this),
            new NgHide($this),
            new NgBind($this),
            new NgClass($this)
        );

        foreach ($defaultAttributes as $directive) {
            $this->addDirective($directive);
        }
    }
}
