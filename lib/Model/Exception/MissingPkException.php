<?php

namespace Mismatch\Model\Exception;

class MissingPkException extends ModelException
{
    public function __construct($model)
    {
        parent::__construct(sprintf(
            "You tried to access %s's primary key, but it " .
            "has no discernible primary key. Either ensure you've " .
            "declared a 'Primary' attribute or set the 'pk' on the " .
            "model's metadata to return your primary attribute.",
            $model));
    }
}
