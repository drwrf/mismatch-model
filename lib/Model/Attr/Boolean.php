<?php

namespace Mismatch\Model\Attr;

class Boolean extends Primitive
{
    protected $default = false;

    /**
     * {@inheritDoc}
     */
    public function cast($value)
    {
        return (bool) $value;
    }
}
