<?php

namespace Mismatch\Model\Attr;

class Integer extends Primitive
{
    protected $default = 0;

    /**
     * {@inheritDoc}
     */
    public function cast($value)
    {
        return (int) $value;
    }
}
