<?php
/*
 * This file is part of the ngPhRender package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile;

use PhCompile\Template\Directive\Directive,
    PhCompile\Template\Directive\NgVisibility,
    PhCompile\Template\Directive\NgRepeat,
    PhCompile\Template\Directive\NgBind;

/**
 * Server side renderer for AngularJS templates.
 *
 * This class is responsible for containing configuration,
 * managing renderers and rendering template.
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
     * Contains all registered attributes with corresponding renderer
     * objects.
     *
     * @var array
     */
    protected $attributeCompilers = array();

    /**
     * Creates new PhRender object.
     */
    public function __construct()
    {
        $this->registerDefaultCompilers();

        $this->config = new Scope();

        $this->setDefaultConfig();
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
                     * Class used to tag server side rendered elements.
                     */
                    'class' => 'ng-phrender',
                    /**
                     * Attribute used to tag server side compiled expressions.
                     */
                    'attr' => 'ng-phrender'
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
     * @param string $accessString Access string to wanted config value.
     * @return mixed Value corresponding to given access string or null if value
     * does not exist.
     */
    public function getConfig($accessString)
    {
        return $this->config->getData($accessString);
    }

    /**
     * Registers Directive object for given attribute.
     * Directive's method compile() will be called each time given attribute is
     * found inside DOM, compile() method is called with DOMElement containing
     * this attribute.
     *
     * @param string $attribute HTML attribute to register.
     * @param Renderer $direvtive Attribute directive.
     */
    public function registerAttributeDirective($attribute, Directive $direvtive)
    {
        $this->attributeCompilers[$attribute] = $direvtive;
    }

    /**
     * Returns Renderer registered for given attribute or null if no compiler
     * has been registered for it.
     *
     * @param string $attribute HTML attribute.
     * @return Compiler|null Renderer object or null.
     */
    public function getAttributeDirective($attribute)
    {
        if (isset($this->attributeCompilers[$attribute])) {
            $renderer = $this->attributeCompilers[$attribute];
        } else {
            $renderer = null;
        }

        return $renderer;
    }

    /**
     * Registers default compilers for AngularJS attributes.
     */
    protected function registerDefaultCompilers()
    {
        $defaultAttributes = array(
            'ng-repeat' => new NgRepeat($this),
            'ng-hide' => new NgVisibility($this),
            'ng-show' => new NgVisibility($this),
            'ng-bind' => new NgBind($this)
        );

        foreach ($defaultAttributes as $attribute => $compiler) {
            $this->registerAttributeDirective($attribute, $compiler);
        }
    }
}