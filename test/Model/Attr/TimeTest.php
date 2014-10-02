<?php

namespace Mismatch\Model\Attr;

class TimeTest extends \PHPUnit_Framework_TestCase
{
    use PrimitiveTestCase;

    public function createSubject($name, $opts = [])
    {
        return new Time($name, $opts);
    }

    public function testRead_timeParsing()
    {
        $subject = $this->createSubject('test');

        $this->assertInstanceOf('DateTime', $subject->read(null, 'now'));
    }
}
