<?php

/**
 * This file is part of Mismatch.
 *
 * @author   ♥ <hi@drwrf.com>
 * @license  MIT
 */
namespace Mismatch;

use Mismatch\Model\Metadata;
use Mismatch\Model\Dataset;
use Mismatch\Model\Attrs;
use Mismatch\Model\Attr\Primary;
use Mismatch\Model\Exception\MissingPkException;
use Mismatch\Model\Exception\InvalidAttrException;

/**
 * Adds model-like functionality to a class.
 *
 * Mismatch models work by including traits that add extra features
 * to them. This trait adds the basic features you'd expect on any
 * model. For example:
 *
 *  - The ability to define attributes
 *  - Magic getters and setters
 *  - Change and state tracking
 *  - And more.
 *
 * As an example, let's define a model that represents users in our
 * application.
 *
 * ```php
 * class User
 * {
 *     user Mismatch\Model;
 *
 *     public static function init($m)
 *     {
 *         $m->email = 'String';
 *         $m->firstName = 'String?';
 *         $m->lastName = 'String?';
 *         $m->created = ['Time', 'default' => 'now'];
 *     }
 * }
 * ```
 *
 * As you can see, all it takes is declaring a class, including the
 * `Mismatch\Model` trait, and defining attributes on the model.  You
 * can specify the types for each attribute, as well as more complex
 * properties of the attribute, such as whether or not it is nullable
 * or the default to use.
 *
 * After declaring a model, you get to interact with it.
 *
 * ```php
 * $user = new User();[
 * $user->email = 'h.donna.gust@example.com',
 * $user->firstName = 'H. Donna';
 * $user->lastName = 'Gust';
 * ```
 *
 * That's all there is to it!
 *
 * ## Metadata
 *
 * All models are backed by an instance of `Mismatch\Model\Metadata`. This
 * is a `Pimple` container that holds all sort of information about the model—
 * its name, its class, its attributes, and more.
 *
 * The first time you access a model its metadata is loaded up and configured
 * and the special `init` hook is called, allowing you (as well as other traits)
 * the ability to add and modify its metadata instance. This is where the real
 * power of Mismatch comes into play—as it's incredibly easy for new traits to
 * add and extend the functionality of your model.
 *
 * Take a look at [the Pimple docs](pimple.sensiolabs.org) for more information
 * on how to interact with the container.
 */
trait Model
{
    /**
     * Returns the metadata for this specific model.
     *
     * @return  Metadata
     */
    public static function metadata()
    {
        return Metadata::get(get_called_class());
    }

    /**
     * Hook for when this trait is used on a class.
     *
     * @param  Metadata  $m
     */
    public static function usingModel($m)
    {
        $m['attrs'] = function($m) {
            return new Attrs($m);
        };

        $m['pk'] = function($m) {
            foreach ($m['attrs'] as $attr) {
                if ($attr instanceof Primary) {
                    return $attr;
                }
            }

            throw new MissingPkException($m->getClass());
        };
    }

    /**
     * @var  Dataset  The dataset that holds all of this model's data
     */
    private $data;

    /**
     * @var  Attrs  The attribute bag that holds all of this model's attrs.
     */
    private $attrs;

    /**
     * @var  Metadata
     */
    private $metadata;

    /**
     * Constructor.
     *
     * @param   Dataset|array  $data
     */
    public function __construct($data = [])
    {
        $this->metadata = static::metadata();

        if (!($data instanceof Dataset)) {
            $data = new Dataset($data);
        }

        $this->data = $data;
    }

    /**
     * Returns a value on the model.
     *
     * If there is a method called `get$Name` on the model then
     * that method will be called and returned. This allows getters
     * and setters to gradually make their way into the model, while
     * keeping property access as the main public interface.
     *
     * This also means that is possible to have "computed" attributes
     * on the model—attributes that are composed of other attributes
     * entirely.
     *
     * ```php
     * class User
     * {
     *     use Mismatch\Model;
     *     // etc...
     *
     *    private function getFullName()
     *    {
     *        return $this->read('firstName') . ' ' . $this->read('lastName');
     *    }
     * }
     *
     * $user = new User([
     *     'firstName' => 'Peter',
     *     'lastName' => 'Pam',
     * ])
     *
     * echo $user->fullName;
     * // Should print "Peter Pam"
     * ```
     *
     * @param   string  $name
     * @return  mixed
     */
    public function __get($name)
    {
        if (method_exists($this, 'get'.$name)) {
            return $this->{'get'.$name}();
        }

        return $this->read($name);
    }

    /**
     * Sets a value on the model.
     *
     * Just like `__get`, this will call a `set$Name` method on the model
     * (given that it exists) and will pass the value along with it.
     *
     * This allows you to perform actions, modify the data, and more before
     * it is actually written to the model.
     *
     * ```php
     * class User
     * {
     *     use Mismatch\Model;
     *     // etc...
     *
     *    private function setFirstName($name)
     *    {
     *        $this->write(trim($name));
     *    }
     * }
     *
     * // Should trim the name on write
     * $user->firstName = '   Wendy Peffercorn   ';
     * ```
     *
     * @param  string  $name
     * @param  mixed   $value
     */
    public function __set($name, $value)
    {
        if (method_exists($this, 'set'.$name)) {
            return $this->{'set'.$name}($value);
        }

        return $this->write($name, $value);
    }

    /**
     * Returns whether or not a value is set on the model.
     *
     * In all cases, this will return true so long as the attribute
     * is set on the model, regardless of whether or not it's null.
     *
     * Otherwise, if the value is set on the model then we will return
     * true.
     *
     * @param  string  $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->attr($name) || $this->data->has($name);
    }

    /**
     * Helpful aid for debugging.
     *
     * @return  string
     */
    public function __toString()
    {
        return sprintf('%s:%s', get_class($this), md5(spl_object_hash($this)));
    }

    /**
     * Returns the id of the model, should it exists.
     *
     * @return  mixed
     */
    public function id()
    {
        return $this->read($this->metadata['pk']->name);
    }

    /**
     * Reads a bare value on the model.
     *
     * Unlike `__get`, this does not call any getters on the model.
     *
     * @param  string  $name
     * @return mixed
     */
    public function read($name)
    {
        $attr = $this->attr($name);
        $value = $this->data->read($name);

        if ($attr) {
            $value = $attr->read($this, $value);
        }

        return $value;
    }

    /**
     * Writes a value to the model.
     *
     * Unlike `__set`, this does not call any setters on the model.
     *
     * @param  string  $name
     * @param  mixed   $value
     */
    public function write($name, $value)
    {
        $attr = $this->attr($name);

        if ($attr) {
            $value = $attr->write($this, $value);
        }

        return $this->data->write($name, $value);
    }

    /**
     * Returns whether or not the value has changed on the model.
     *
     * @param   string  $name
     * @return  bool
     */
    public function changed($name)
    {
        return $this->data->changed($name);
    }

    /**
     * Returns the original and changed value on a model.
     *
     * @param  string  $name
     * @return [$old, $new]|null
     */
    public function diff($name)
    {
        return $this->data->diff($name);
    }

    /**
     * Whether or not the model is persisted.
     *
     * @return  bool
     */
    public function isPersisted()
    {
        return $this->data->isPersisted();
    }

    /**
     * Returns an attribute instance for this model.
     *
     * @return  AttrInterface
     */
    private function attr($name)
    {
        if (!$this->attrs) {
            $this->attrs = $this->metadata['attrs'];
        }

        try {
            return $this->attrs->get($name);
        } catch (InvalidAttrException $e) {
            return null;
        }
    }
}
