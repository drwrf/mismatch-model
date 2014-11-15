<?php

/**
 * This file is part of Mismatch.
 *
 * @author   ♥ <hi@drwrf.com>
 * @license  MIT
 */
namespace Mismatch\Model\Attr;

/**
 * A type for set attributes.
 */
class Set extends Primitive
{
    /**
     * {@inheritDoc}
     */
    protected $default = [];

    /**
     * {@inheritDoc}
     */
    public function cast($values)
    {
        if (!$this->each) {
            return (array) $values;
        }

        $ret = [];

        foreach ($values as $value) {
            if ($this->each) {
                $value = $this->each->cast($value);
            }

            $ret[] = $value;
        }

        return $ret;
    }
}
