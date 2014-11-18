<?php

namespace Mismatch\Model\Exception;

class InvalidTypeException extends ModelException
{
    public function __construct($model, $attr, $type)
    {
        parent::__construct(sprintf(
            "The type you declared for '%s:%s' is invalid. You specified " .
            "'%s', but it was unusable. You can either pass a valid " .
            "string or a valid callback that returns an instance of " .
            "'Mismatch\Model\Attr\AttrInterface'.",
            $model, $attr, $type));
    }
}
