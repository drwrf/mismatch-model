<?php

namespace Mismatch\Model\Attr;

class FloatTest extends \PHPUnit_Framework_TestCase
{
    use PrimitiveTestCase;

    public function createSubject($name, $opts = [])
    {
        return new Float($name, $opts);
    }
}
