<?php

namespace Mismatch\Model\Attr;

class Float extends Primitive
{
    protected $default = 0.0;

    /**
     * {@inheritDoc}
     */
    public function cast($value)
    {
        return (float) $value;
    }
}
