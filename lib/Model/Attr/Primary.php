<?php

namespace Mismatch\Model\Attr;

use Mismatch\Model\Attrs;
use Mismatch\Model\Attr\Primitive;
use Mismatch\Model\Attr\AttrInterface;

class Primary extends String
{
    /**
     * {@inheritDoc}
     */
    protected $nullable = true;

    /**
     * {@inheritDoc}
     */
    protected $default = null;

    /**
     * {@inheritDoc}
     */
    protected $serialize = AttrInterface::SERIALIZE_PRIMARY;
}
