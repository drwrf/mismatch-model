<?php

namespace Mismatch\Model\Attr;

class String extends Primitive
{
    protected $default = '';

    /**
     * {@inheritDoc}
     */
    public function cast($value)
    {
        return (string) $value;
    }
}
