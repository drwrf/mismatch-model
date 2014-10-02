<?php

/**
 * This file is part of Mismatch.
 *
 * @author   ♥ <hi@drwrf.com>
 * @license  MIT
 */
namespace Mismatch\Model\Attr;

use DateTime;
use DateTimeZone as TZ;

/**
 * A type for time attributes.
 */
class Time extends Primitive
{
    /**
     * {@inheritDoc}
     */
    protected $default = 'now';

    /**
     * @var  string  The format to use when writing to the native datasource
     */
    protected $format = 'Y-m-d H:i:s';

    /**
     * @var   string  The timezone for the time.
     */
    protected $timezone = 'UTC';

    /**
     * {@inheritDoc}
     */
    public function toNative($value)
    {
        return parent::toNative($value)->format($this->format);
    }

    /**
     * {@inheritDoc}
     */
    public function cast($value)
    {
        if (!($value instanceof DateTime)) {
            $value = new DateTime($value, new TZ($this->timezone));
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefault($model)
    {
        $default = parent::getDefault($model);

        if (is_string($default)) {
            return new DateTime($default, new TZ($this->timezone));
        }

        return $default;
    }
}
