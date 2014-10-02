<?php

namespace Mismatch\Model\Attr;

class StringTest extends \PHPUnit_Framework_TestCase
{
    use PrimitiveTestCase;

    public function createSubject($name, $opts = [])
    {
        return new String($name, $opts);
    }
}
