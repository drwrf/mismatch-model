<?php

/**
 * This file is part of Mismatch.
 *
 * @author   ♥ <hi@drwrf.com>
 * @license  MIT
 */
namespace Mismatch\Model\Attr;

/**
 * A type for string attributes.
 */
class String extends Primitive
{
    /**
     * {@inheritDoc}
     */
    protected $default = '';

    /**
     * {@inheritDoc}
     */
    public function cast($value)
    {
        return (string) $value;
    }
}
