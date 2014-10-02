<?php

namespace Mismatch\Model\Attr;

class IntegerTest extends \PHPUnit_Framework_TestCase
{
    use PrimitiveTestCase;

    public function createSubject($name, $opts = [])
    {
        return new Integer($name, $opts);
    }
}
