<?php

namespace Mismatch\Model\Exception;

class MissingAttrsException extends ModelException
{
    public function __construct($model)
    {
        parent::__construct(sprintf(
            "You must set an 'attrs' key on '%s' model's metadata before " .
            "accessing attributes. This may be as simple as adding a " .
            "'use Mismatch\Model' or ensuring that you have used it " .
            "before another trait that requires the 'attrs' key.",
            $model));
    }
}
