<?php

namespace Mismatch\Model\Attr;

interface AttrInterface
{
    /**
     * @const  int  Don't serialize the attribute.
     */
    const SERIALIZE_NONE = 0;

    /**
     * @const  int  Serialize the friggin attribute!
     */
    const SERIALIZE_VALUE = 1;

    /**
     * Called when writing a value to the model in PHP land.
     *
     * @param   Mismatch\Model  $model
     * @param   mixed           $value
     * @return  mixed
     */
    public function write($model, $value);

    /**
     * Called when reading a value from a model.
     *
     * @param   Mismatch\Model  $model
     * @param   mixed           $value
     * @return  mixed
     */
    public function read($model, $value);

    /**
     * Called when reading a value from the datasource and it needs
     * to be turned into a native PHP type.
     *
     * @param   mixed  $result
     * @param   mixed  $value
     * @return  mixed
     */
    public function deserialize($result, $value);

    /**
     * Called when reading a value from a model that needs to
     * be turned into a native type for the datasource.
     *
     * @param   Mismatch\Model  $model
     * @param   mixed           $old
     * @param   mixed           $new
     * @return  mixed
     */
    public function serialize($model, $old, $new);
}
