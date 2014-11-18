<?php

namespace Mismatch\Model\Exception;

class InvalidAttrException extends ModelException
{
    public function __construct($model, $attr)
    {
        parent::__construct(sprintf(
            "You tried to access an attribute named '%s' on the '%s' " .
            "model, but it doesn't exist. Are you sure you've declared it?",
            $attr, $model));
    }
}
