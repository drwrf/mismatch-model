<?php

/**
 * This file is part of Mismatch.
 *
 * @author   ♥ <hi@drwrf.com>
 * @license  MIT
 */
namespace Mismatch\Model;

/**
 * Holds the data for a model.
 */
class Dataset
{
    /**
     * @var  array  The raw array of values.
     */
    private $data = [];

    /**
     * @var  array  The changed values on the dataset.
     */
    private $changes = [];

    /**
     * @var  bool  Whether or not the dataset has been persisted.
     */
    private $persisted = false;

    /**
     * @var  bool  Whether or not the dataset has been destroyed.
     */
    private $destroyed = false;

    /**
     * Constructor.
     *
     * @param  array  $data  The original set of data for the dataset.
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Returns a JSON string of the data for debugging.
     *
     * @return  string
     */
    public function __toString()
    {
        return json_encode(array_merge($this->data, $this->changes));
    }

    /**
     * Reads a value from the dataset.
     *
     * @param   string  $name
     * @return  bool
     */
    public function has($name)
    {
        if (array_key_exists($name, $this->changes)) {
            return true;
        }

        if (array_key_exists($name, $this->data)) {
            return true;
        }

        return false;
    }

    /**
     * Reads a value from the dataset.
     *
     * @param   string  $name
     * @param   mixed   $default
     * @return  mixed
     */
    public function read($name, $default = null)
    {
        if (array_key_exists($name, $this->changes)) {
            return $this->changes[$name];
        }

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return $default;
    }

    /**
     * Changes a value or set of values on the dataset.
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  self
     */
    public function write($name, $value)
    {
        // Mark the field as unchanged if the value has not deviated
        // from the original value that the dataset holds. We get slightly
        // smarter change tracking this way.
        if (array_key_exists($name, $this->data) && $this->data[$name] === $value) {
            unset($this->changes[$name]);
            return $this;
        }

        $this->changes[$name] = $value;

        return $this;
    }

    /**
     * Returns the diff of a value.
     *
     * @param   string  $name
     * @return  array|null
     */
    public function diff($name)
    {
        // Return no diff when nothing has changed and the model isn't
        // persisted. This is useful for new models who need to save
        // initial values even if they weren't technically "changed".
        if ($this->persisted && !$this->isChanged($name)) {
            return null;
        }

        if (array_key_exists($name, $this->data)) {
            $old = $this->data[$name];
        } else {
            $old = null;
        }

        if (array_key_exists($name, $this->changes)) {
            $new = $this->changes[$name];
        } else {
            $new = null;
        }

        // Non-persisted entities may have never had changes, but
        // we still want to consider initial values a diff.
        if (!$this->persisted && $new === null) {
            return [null, $old];
        } else {
            return [$old, $new];
        }
    }

    /**
     * Returns whether or not a value has changed.
     *
     * Interestingly, this will return true so long as the value
     * has been written to, regardless of whether or not the value
     * has already changed.
     *
     * @param   string  $name
     * @return  bool
     */
    public function isChanged($name = null)
    {
        if ($name === null) {
            return (bool) $this->changes;
        }

        return array_key_exists($name, $this->changes);
    }

    /**
     * Marks the dataset as persisted.
     */
    public function markPersisted()
    {
        $this->persisted = true;
        $this->destroyed = false;

        // The dataset is persisted, so turn all changes into
        // the new, canonical version of the data.
        $this->data = array_merge($this->data, $this->changes);

        return $this;
    }

    /**
     * Returns whether or not the dataset is persisted.
     *
     * @return  bool
     */
    public function isPersisted()
    {
        return $this->persisted;
    }

    /**
     * Marks the dataset as destroyed.
     */
    public function markDestroyed()
    {
        $this->destroyed = true;
        $this->persisted = false;
        $this->data = [];
        $this->changes = [];

        return $this;
    }

    /**
     * Returns whether or not the dataset is destroyed.
     *
     * @return  bool
     */
    public function isDestroyed()
    {
        return $this->destroyed;
    }
}
