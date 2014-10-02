<?php

namespace Mismatch\Model\Attr;

class SetTest extends \PHPUnit_Framework_TestCase
{
    use PrimitiveTestCase;

    public function createSubject($name, $opts = [])
    {
        return new Set($name, $opts);
    }
}
