<?php

namespace Mismatch\Model;

use Pimple\Container;
use ReflectionClass;
use BadMethodCallException;

/**
 * Metadata is the core of all Mismatch models.
 *
 * In its simplest form, the metadata container is a pimple container
 * that allows setting services and other attributes directly on the model.
 *
 * The real magic, however, comes from its powerful callback system. Any
 * trait defined on a model that defines an initializer method will have
 * that method run with the newly constructed metadata object.
 *
 * This allows traits to directly hook into the construction of a new mismatch
 * object, and is how all of the magical special powers that mismatch models
 * have come into being.
 *
 * ```php
 * trait Example
 * {
 *     public static function usingExample($metadata)
 *     {
 *         // This method will be run the first time a class is accessed
 *         // using Mismatch::metadata($class). Since you have the metadata
 *         // object (and it's an open pimple container) you can add
 *         // or modify any part of the metadata as you please.
 *         $metadata['test'] = true;
 *     }
 * }
 * ```
 */
class Metadata extends Container
{
    /**
     * @var  Metadata[]
     */
    private static $instances = [];

    /**
     * Factory for getting a class' Metadata.
     *
     * This ensures that the same Metadata instance is re-used for
     * all instances of a class.
     *
     * @param   string  $class
     * @return  Metadata
     */
    public static function get($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = new Metadata($class);
        }

        return static::$instances[$class];
    }

    /**
     * @var  ReflectionClass
     */
    private $class;

    /**
     * @var  array
     */
    private $parents;

    /**
     * @var  array
     */
    private $traits;

    /**
     * Constructor.
     *
     * @param  string  $class
     */
    public function __construct($class)
    {
        parent::__construct();

        $this->class = new ReflectionClass($class);

        // Expose the class name as the "name" of the metadata
        // container. This can be changed if necessary.
        $this['name'] = function() {
            return $this->class->getName();
        };

        // Allow traits to define callbacks that run when included in a model.
        foreach ($this->getTraits() as $trait) {
            if ($method = $this->methodForTrait($trait)) {
                call_user_func($method, $this);
            }
        }

        // At this point, it's time to initialize the class and let it
        // declare any properties or other metadata that it wants.
        if (method_exists($class, 'init') && is_callable([$class, 'init'])) {
            $class::init($this);
        }
    }

    /**
     * Returns an attribute on the Metadata instance.
     *
     * @return  Attr/AttrInterface
     */
    public function __get($name)
    {
        return $this['attrs']->get($name);
    }

    /**
     * Adds an attribute to the Metadata instance.
     *
     * @param   string  $name
     * @param   mixed   $type
     */
    public function __set($name, $type)
    {
        if (!isset($this['attrs'])) {
            throw new BadMethodCallException(
                'You must set an "attrs" key on your metadata before trying ' .
                'to add attributes. This may be as simple as adding a ' .
                '"use Mismatch\Model" or ensuring that you have used it ' .
                'before another trait that requires the "attrs" key.');
        }

        $this['attrs']->set($name, $type);
    }

    /**
     * Returns the FQCN that this metadata is for.
     *
     * @return  string
     */
    public function getClass()
    {
        return $this->class->getName();
    }

    /**
     * The namespace that this class is contained within.
     *
     * @return  string
     */
    public function getNamespace()
    {
        return $this->class->getNamespaceName();
    }

    /**
     * Returns the parents of the class.
     *
     * Parents will be returned in order of evaluation, which means
     * that your most distant relative will be first in the list
     * and your direct parent will be last.
     *
     * @return  array
     */
    public function getParents()
    {
        if (!$this->parents) {
            $parents = class_parents($this->getClass());
            $parents = array_reverse($parents);
            $parents = array_keys($parents);
            $this->parents = $parents;
        }

        return $this->parents;
    }

    /**
     * A list of traits that the class uses.
     *
     * Just like `getParents`, the list of traits is returned in order of
     * evaluation. Traits are also recursively discovered, which means that
     * if a trait your class uses also uses a trait, that will be included
     * in the list.
     *
     * @return  array
     */
    public function getTraits()
    {
        if (!$this->traits) {
            $traits = [];

            foreach ($this->getParents() as $parent) {
                $traits = array_merge($traits, $this->listTraits($parent));
            }

            // Include traits for the class we're actually working with.
            $traits = array_merge($traits, $this->listTraits($this->getClass()));
            $traits = array_unique($traits);
            $this->traits = $traits;
        }

        return $this->traits;
    }

    /**
     * Returns the full list of traits that a given class uses.
     *
     * @return  array
     */
    private function listTraits($class)
    {
        $traits = [];

        foreach (class_uses($class) as $trait) {
            $traits = array_merge($traits, $this->listTraits($trait), [$trait]);
        }

        return $traits;
    }

    /**
     * @return  callable
     */
    private function methodForTrait($trait)
    {
        $method = substr($trait, strrpos($trait, '\\') + 1);
        $method = 'using' . $method;
        $callable = [$this->getClass(), $method];

        // We need both the method_exists and is_callable to ensure
        // that the method is *actually* defined. Calling a method
        // with __callStatic is not allowed and generally bad.
        if (method_exists($this->getClass(), $method) && is_callable($callable)) {
            return [$this->getClass(), $method];
        }
    }
}
