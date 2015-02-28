<?php
/*
 * This file is part of the ngPhRender package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhRender;

use PhRender\Template\Renderer\Renderer,
    PhRender\Template\Renderer\NgVisibility,
    PhRender\Template\Renderer\NgRepeat,
    PhRender\Template\Renderer\NgBind;

/**
 * Server side renderer for AngularJS templates.
 *
 * This class is responsible for containing configuration,
 * managing renderers and rendering template.
 */
class PhRender
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
    protected $attributeRenderers = array();
    
    public function __construct()
    {
        $this->registerDefaultRenderers();

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
                'render' => array(
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
     * Registers Renderer object for given attribute.
     * Renderer's method render() will be called each time given attribute is
     * found inside DOM, render() method is called with DOMElement containing
     * this attribute.
     *
     * @param string $attribute HTML attribute to register.
     * @param Renderer $renderer Attribute renderer.
     */
    public function registerAttributeRenderer($attribute, Renderer $renderer)
    {
        $this->attributeRenderers[$attribute] = $renderer;
    }

    /**
     * Returns Renderer registered for given attribute or null if no Renderer
     * has been registered for it.
     *
     * @param string $attribute HTML attribute.
     * @return Renderer|null Renderer object or null.
     */
    public function getAttributeRenderer($attribute)
    {
        if (isset($this->attributeRenderers[$attribute])) {
            $renderer = $this->attributeRenderers[$attribute];
        } else {
            $renderer = null;
        }

        return $renderer;
    }

    /**
     * Registers default renderers for AngularJS attributes.
     */
    protected function registerDefaultRenderers()
    {
        $defaultAttributes = array(
            'ng-repeat' => new NgRepeat($this),
            'ng-hide' => new NgVisibility($this),
            'ng-show' => new NgVisibility($this),
            'ng-bind' => new NgBind($this)
        );

        foreach ($defaultAttributes as $attribute => $renderer) {
            $this->registerAttributeRenderer($attribute, $renderer);
        }
    }
}