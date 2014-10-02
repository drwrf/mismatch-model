<?php

namespace Mismatch;

use Mismatch\Model\Metadata;
use Mismatch\Model\Dataset;
use Mismatch\Model\Attrs;
use InvalidArgumentException;

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
    }

    /**
     * @var  Dataset
     */
    public $data;

    /**
     * @var  AttributeBag
     */
    private $attrs;

    /**
     * Constructor.
     *
     * @param   Dataset|array  $data
     */
    public function __construct($data = [])
    {
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
            $this->attrs = static::metadata()['attrs'];
        }

        try {
            return $this->attrs->get($name);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }
}
