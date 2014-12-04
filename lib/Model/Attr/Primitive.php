<?php

/**
 * This file is part of Mismatch.
 *
 * @author   ♥ <hi@drwrf.com>
 * @license  MIT
 */
namespace Mismatch\Model\Attr;

use BadMethodCallException;

/**
 * A helpful base class for attributes that represent primitive types.
 */
abstract class Primitive extends Attr
{
    /**
     * {@inheritDoc}
     */
    protected $serialize = AttrInterface::SERIALIZE_VALUE;

    /**
     * {@inheritDoc}
     */
    public function read($model, $value)
    {
        if ($value === null) {
            return $this->nullable ? null : $this->getDefault($model);
        }

        return $this->castToPHP($value);
    }

    /**
     * {@inheritDoc}
     */
    public function write($model, $value)
    {
        if ($value === null && $this->nullable) {
            return null;
        }

        return $this->castToPHP($value);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize($model, $old, $new)
    {
        if ($new === null) {
            return $this->nullable ? null : $this->getDefault($model);
        }

        return $this->castToNative($new);
    }

    /**
     * {@inheritDoc}
     */
    public function deserialize($result, $value)
    {
        if ($value === null && $this->nullable) {
            return null;
        }

        return $this->castToPHP($value);
    }

    /**
     * Hook for casting to the PHP type.
     *
     * @param   mixed  $value
     * @return  mixed
     */
    public function castToPHP($value)
    {
        return $this->cast($value);
    }

    /**
     * Hook for casting to the native type.
     *
     * @param   mixed  $value
     * @return  mixed
     */
    public function castToNative($value)
    {
        return $this->cast($value);
    }

    /**
     * Should return the value casted to an appropriate type.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function cast($value)
    {
        throw new BadMethodCallException(
            'Primitive attributes must override either "cast" '.
            'or both "castToPHP" and "castToNative" to properly '.
            'implement the interface');
    }

    /**
     * Should return the default value for the type.
     *
     * @return mixed
     */
    protected function getDefault($model)
    {
        if (is_callable($this->default)) {
            return call_user_func($this->default, $model);
        }

        return $this->default;
    }
}
