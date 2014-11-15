<?php

namespace Mismatch\Model\Attr;

use Mismatch\Model\Attrs;
use Mismatch\Model\Attr\Primitive;
use Mismatch\Model\Attr\AttrInterface;

class Primary extends Primitive
{
    /**
     * {@inheritDoc}
     */
    protected $each = 'Mismatch\Model\Attr\Integer';

    /**
     * {@inheritDoc}
     */
    protected $nullable = true;

    /**
     * {@inheritDoc}
     */
    protected $serialize = AttrInterface::SERIALIZE_NONE;

    /**
     * {@inheritDoc}
     */
    public function cast($value)
    {
        return $this->each()->cast($value);
    }

    /**
     * @return  Mismatch\Model\Attr\AttrInterface
     */
    private function each()
    {
        if (is_string($this->each)) {
            $this->each = new $this->each($this->name, [
                'metadata' => $this->metadata,
                'parent'   => $this,
            ]);
        }

        return $this->each;
    }
}
