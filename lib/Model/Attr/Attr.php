<?php

/**
 * @author   ♥ <hi@drwrf.com>
 * @license  MIT
 */
namespace Mismatch\Model\Attr;

/**
 * A helpful base class for all attributes.
 */
abstract class Attr implements AttrInterface
{
    /**
     * The name of the attribute, which dictates the key that it is
     * retrieved and stored under.
     *
     * @var  string
     */
    protected $name;

    /**
     * The key of the attribute, which dictates the place that it is
     * stored and retrieved from externally.
     *
     * @var  string
     */
    protected $key;

    /**
     * Whether or not the attribute is nullable. If it is true, then
     * "null"s written to the model will be written untouched.
     *
     * @var  bool
     */
    protected $nullable = false;

    /**
     * A default value for the attribute.
     *
     * @var  mixed
     */
    protected $default;

    /**
     * The strategy to use for serialization.
     *
     * @var  int
     */
    protected $serialize = AttrInterface::SERIALIZE_NONE;

    /**
     * The metadata of the model owning this attribute.
     *
     * @var  Mismatch\Model\Metadata
     */
    protected $metadata;

    /**
     * Constructor.
     *
     * @param  string  $name
     * @param  array   $opts
     */
    public function __construct($name, array $opts = [])
    {
        $this->name = $name;

        if (empty($opts['key'])) {
            $opts['key'] = $name;
        }

        foreach ($opts as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Provides read-only access to properties.
     *
     * @param  string
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }
}
