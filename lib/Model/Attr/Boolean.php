<?php

/**
 * This file is part of Mismatch.
 *
 * @author   ♥ <hi@drwrf.com>
 * @license  MIT
 */
namespace Mismatch\Model\Attr;

/**
 * A type for boolean attributes.
 */
class Boolean extends Primitive
{
    /**
     * {@inheritDoc}
     */
    protected $default = false;

    /**
     * {@inheritDoc}
     */
    public function cast($value)
    {
        return (bool) $value;
    }
}
