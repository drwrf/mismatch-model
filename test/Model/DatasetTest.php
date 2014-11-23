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

    public function test_isChanged_worksForDifferentValues()
    {
        $this->subject->write('original', false);
        $this->assertFalse($this->subject->isChanged('invalid'));
        $this->assertTrue($this->subject->isChanged('original'));
        $this->assertTrue($this->subject->isChanged());
    }

    public function test_diff_ignoresPersistedAndUnchanged()
    {
        $this->subject->markPersisted();
        $this->subject->write('original', true);
        $this->assertNull($this->subject->diff('original'));
    }

    public function test_diff_handlesUnpersisted()
    {
        $this->subject->write('original', true);
        $this->assertEquals([null, true], $this->subject->diff('original'));
    }

    public function test_diff_handlesChanges()
    {
        $this->subject->write('original', false);
        $this->subject->write('changed', true);
        $this->assertEquals([true, false], $this->subject->diff('original'));
        $this->assertEquals([null, true], $this->subject->diff('changed'));
    }

    public function test_diff_handlesChanges_whenPersisted()
    {
        $this->subject->markPersisted();
        $this->subject->write('original', false);
        $this->subject->write('changed', true);
        $this->assertEquals([true, false], $this->subject->diff('original'));
        $this->assertEquals([null, true], $this->subject->diff('changed'));
    }

    public function test_write_ignoresSameValues()
    {
        $this->subject->write('original', true);
        $this->assertFalse($this->subject->isChanged('invalid'));
        $this->assertFalse($this->subject->isChanged('original'));
        $this->assertFalse($this->subject->isChanged());
    }

    public function test_isPersisted()
    {
        $this->subject->markPersisted();
        $this->assertTrue($this->subject->isPersisted());
        $this->assertFalse($this->subject->isDestroyed());
        $this->assertFalse($this->subject->isChanged());
    }

    public function test_isDestroyed()
    {
        $this->subject->markPersisted();
        $this->subject->markDestroyed();
        $this->assertTrue($this->subject->isDestroyed());
        $this->assertFalse($this->subject->isPersisted());
        $this->assertFalse($this->subject->isChanged());
    }
}
