<?php

namespace Mismatch\Model;

class DatasetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->subject = new Dataset([
            'original' => true
        ]);
    }

    public function test_has_worksForOriginalValues()
    {
        $this->assertTrue($this->subject->has('original'));
        $this->assertFalse($this->subject->has('invalid'));
    }

    public function test_has_worksForChangedValues()
    {
        $this->subject->write('original', false);
        $this->assertTrue($this->subject->has('original'));
    }

    public function test_read_worksForOriginalValues()
    {
        $this->assertTrue($this->subject->read('original'));
    }

    public function test_read_worksForChangeValues()
    {
        $this->subject->write('original', false);
        $this->assertFalse($this->subject->read('original'));
    }

    public function test_change_worksForDifferentValues()
    {
        $this->subject->write('original', false);
        $this->assertFalse($this->subject->isChanged('invalid'));
        $this->assertTrue($this->subject->isChanged('original'));
        $this->assertTrue($this->subject->isChanged());
    }

    public function test_write_ignoresSameValues()
    {
        $this->subject->write('original', true);
        $this->assertFalse($this->subject->isChanged('invalid'));
        $this->assertFalse($this->subject->isChanged('original'));
        $this->assertFalse($this->subject->isChanged());
    }
}
