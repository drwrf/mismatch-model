<?php

/**
 * This file is part of Mismatch.
 *
 * @author   ♥ <hi@drwrf.com>
 * @license  MIT
 */
namespace Mismatch\Model;

use Mismatch\Model\Attr\AttrInterface;
use InvalidArgumentException;
use IteratorAggregate;
use ArrayIterator;

/**
 * Manages access to a model's attributes.
 */
class Attrs implements IteratorAggregate
{
    /**
     * @var  array
     */
    private static $types = [
        'Primary' => 'Mismatch\\Model\\Attr\\Primary',
        'Integer' => 'Mismatch\\Model\\Attr\\Integer',
        'Float'   => 'Mismatch\\Model\\Attr\\Float',
        'String'  => 'Mismatch\\Model\\Attr\\String',
        'Boolean' => 'Mismatch\\Model\\Attr\\Boolean',
        'Time'    => 'Mismatch\\Model\\Attr\\Time',
        'Set'     => 'Mismatch\\Model\\Attr\\Set',
    ];

    /**
     * Registers a type.
     *
     * Types can either be a simple string (which is assumed
     * to be a valid class name) or a callable, which will
     * be called with the name and options of the new type.
     *
     * @param  string          $name
     * @param  string|Closure  $class
     */
    public static function registerType($name, $class)
    {
        static::$types[$name] = $class;
    }

    /**
     * Returns the list of available types, by name.
     *
     * @return  array
     */
    public static function availableTypes()
    {
        return array_keys(static::$types);
    }

    /**
     * @var  array
     */
    private $attrs = [];

    /**
     * @var  Metadata
     */
    private $metadata;

    /**
     * Constructor.
     *
     * @param  Metadata  $metadata
     */
    public function __construct($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return  string
     */
    public function __toString()
    {
        return sprintf('%s:%s', get_class($this), json_encode(array_keys($this->attrs)));
    }

    /**
     * Adds a new attribute to the bag.
     *
     * Attributes must have a name and type, which can be
     * in a few forms:
     *
     *  - A valid type name, like "Integer".
     *  - A valid class name, like "Foo\Bar\MyType".
     *  - A composite type, like "Array[Integer]".
     *  - A nullable type, like "Integer?".
     *  - An AttrInterface object.
     *
     * @param  string  $name
     * @param  mixed   $type
     */
    public function set($name, $type)
    {
        $this->attrs[$name] = $type;

        return $this;
    }

    /**
     * Returns an attribute from the bag.
     *
     * @param   string  $name
     * @return  AttrInterface
     * @throws  InvalidArgumentException
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new InvalidArgumentException();
        }

        if (!($this->attrs[$name] instanceof AttrInterface)) {
            $this->attrs[$name] = $this->buildAttr($name);
        }

        return $this->attrs[$name];
    }

    /**
     * Returns whether or not an attribute has been added to the bag.
     *
     * @param   string  $name
     * @return  bool
     */
    public function has($name)
    {
        return isset($this->attrs[$name]);
    }

    /**
     * Allows iterating over the list of types.
     *
     * @return  ArrayIterator
     */
    public function getIterator()
    {
        $attrs = [];

        foreach ($this->attrs as $key => $val) {
            $attrs[$key] = $this->get($key);
        }

        return new ArrayIterator($attrs);
    }

    /**
     * @param  string  $name
     */
    private function buildAttr($name)
    {
        $opts = $this->attrs[$name];

        // Allow passing a bare string for the type.
        // We can figure out the rest for the user.
        if (is_string($opts)) {
            $opts = ['type' => $opts];
        }

        // Allow passing an array where the first value, regardless
        // of key, is the attribute type to use. This looks pretty.
        if (is_array($opts) && is_int(key($opts))) {
            $opts['type'] = $opts[key($opts)];
            unset($opts[key($opts)]);
        }

        $opts = array_merge($this->parseType($opts), [
            'metadata' => $this->metadata
        ]);

        // Allow types to be factories that can generate instances.
        // This allows more robust customization than simple instances.
        if (is_callable($opts['type'])) {
            return call_user_func($opts['type'], $name, $opts);
        }

        // Build up the type
        return new $opts['type']($name, $opts);
    }

    /**
     * @param   array $opts
     * @return  array
     */
    private function parseType(array $opts)
    {
        if (empty($opts['type'])) {
            throw new InvalidArgumentException();
        }

        $pattern = "/^(?<type>[\w\\\]+)(\[(?<each>[\w\\\]+)\])?(?<null>\?)?$/";

        if (false === preg_match($pattern, $opts['type'], $matches)) {
            throw new InvalidArgumentException();
        }

        // Resolve the type with the already declared types.
        $opts['type'] = $this->resolveType($matches['type']);

        // Parse strings like "Foo" or "Foo?". A question mark at
        // the end of a string indicates the type is nullable.
        if (!empty($matches['null'])) {
            $opts['nullable'] = true;
        }

        // Parse strings like "Foo[Bar]" to be nested types.
        // We'll allow the parent type to figure out how to deal
        // with that nested type.
        if (!empty($matches['each'])) {
            $opts['each'] = $matches['each'];
        }

        return $opts;
    }

    /**
     * @param  string  $type
     * @return string
     */
    private function resolveType($type)
    {
        return isset(static::$types[$type]) ? static::$types[$type] : $type;
    }
}
