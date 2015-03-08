<?php
/*
 * This file is part of the phCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Template\Directive;

use PhCompile\PhCompile,
    PhCompile\Scope,
    InvalidArgumentException;

/**
 * Start point for creating other Directives.
 * Directive objects are used for parsing DOM elements with given data.
 */
abstract class Directive
{
    /**
     * PhCompile object reference.
     *
     * @var PhCompile
     */
    protected $phCompile = null;

    /**
     * Indicates if compiler should stop further compiling of current DOM element.
     *
     * @var bool
     */
    protected $interrupt = false;

    /**
     * Directive restriction, can be element, attribute or class.
     *
     * @var int
     */
    protected $restrict;

    /**
     * Default directive priority.
     *
     * @var int
     */
    protected $priority = 0;

    /**
     * Name that identifies directive.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Creates new directive.
     *
     * @param PhCompile $phCompile PhRender object.
     */
    public function __construct(PhCompile $phCompile)
    {
        $this->phCompile = $phCompile;
        $this->restrict  = 'ACE';
    }

    /**
     * Returns directive name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets directive name.
     *
     * @param string $name Directive name
     * @throws InvalidArgumentException Throws exception if given name is not a string.
     */
    protected function setName($name)
    {
        if (is_string($name) === false) {
            throw new InvalidArgumentException(
            sprintf(
                'Directive name must be a string, "%s" given!', gettype($name)
            )
            );
        }

        $this->name = $name;
    }

    /**
     * Sets directive restriction just like in AngularJS.
     * Supported restrictions are:
     *  'A' - attribute,
     *  'E' - element,
     *  'C' - class.
     * You can combine them as you want e.g. 'AE', 'AC', 'EC' etc.
     *
     * @param string $restrict New directive restriction.
     * @throws InvalidArgumentException Throws exception if given restrict is not a string.
     */
    public function setRestrict($restrict)
    {
        if (is_string($restrict) === false) {
            throw new InvalidArgumentException(
            sprintf(
                'Directive restrict must be a string, "%s" given!',
                gettype($restrict)
            )
            );
        }

        $this->restrict = strtoupper($restrict);
    }

    /**
     * Returns directive restriction.
     *
     * @return int Directive restriction.
     */
    public function getRestrict()
    {
        return $this->restrict;
    }

    /**
     * Tells if directive has given restriction.
     * You should check for only one restrict at a time!
     *
     * @param string $restrict Single letter directive restrict to check.
     * @return boolean True if directive has given restrict, false otherwise.
     * @throws InvalidArgumentException Throws exception if restrict is not a string
     * or it contains more then one letter.
     */
    public function isRestrict($restrict)
    {
        if (is_string($restrict) === false) {
            throw new InvalidArgumentException(
            sprintf(
                'Directive restrict must be a string, "%s" given!',
                gettype($restrict)
            )
            );
        }
        if (strlen($restrict) !== 1) {
            throw new InvalidArgumentException(
            'You should check for only one restrict at a time!'
            );
        }

        return strpos($this->getRestrict(), strtoupper($restrict)) !== false;
    }

    /**
     * Sets directive priority.
     * Default directive priority is 0.
     *
     * @param int $priority New directive priority.
     */
    public function setPriority($priority)
    {
        if (is_numeric($priority) === false) {
            throw new InvalidArgumentException(
            sprintf(
                'Directive priority must be a number, "%s" given!',
                gettype($priority)
            )
            );
        }

        $this->priority = $priority;
    }

    /**
     * Returns directive priority.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Renders and returns given DOM element using given Scope.
     *
     * @param \DOMElement $domElement DOM element to render.
     * @param Scope $scope Scope object containing data for rendering.
     * @return \DOMElement Renderer DOM element.
     */
    public function compile(\DOMElement $domElement, Scope $scope)
    {
        return $domElement;
    }

    /**
     * Sets the boolean telling compiler if it should stop compiling current element.
     * @param boolean $interrupt True if compiler should stop compiling, false otherwise.
     */
    public function setInterrupt($interrupt)
    {
        $this->interrupt = $interrupt;
    }

    /**
     * Returns boolean telling if compiler should stop compiling current DOM element.
     *
     * @return bool Tells compiler if it should stop further compiling of certain
     * DOM element.
     */
    public function doesInterrupt()
    {
        return $this->interrupt;
    }
}