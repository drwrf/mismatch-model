<?php

namespace Mismatch\Model\Attr;

class BooleanTest extends \PHPUnit_Framework_TestCase
{
    use PrimitiveTestCase;

    public function createSubject($name, $opts = [])
    {
        return new Boolean($name, $opts);
    }
}
