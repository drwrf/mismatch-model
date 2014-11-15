<?php

namespace Mismatch\Model\Attr;

use StdClass;

trait PrimitiveTestCase
{
    public function testRead_nullable()
    {
        $subject = $this->createSubject('test', [
            'nullable' => true,
        ]);

        $this->assertNull($subject->read(null, null));
    }

    public function testRead_default()
    {
        $default = new StdClass();
        $subject = $this->createSubject('test', [
            'default' => $default,
            'nullable' => false,
        ]);

        $this->assertEquals($default, $subject->read(null, null));
    }

    public function testRead_default_callable()
    {
        $default = new StdClass();
        $subject = $this->createSubject('test', [
            'default' => function () use ($default) {
                return $default;
            },
            'nullable' => false,
        ]);

        $this->assertSame($default, $subject->read(null, null));
    }

    public function testRead_value()
    {
        $subject = $this->createSubject('test');

        $this->assertNotNull($subject->read(null, 'now'));
    }

    public function testWrite_nullable()
    {
        $subject = $this->createSubject('test', [
            'nullable' => true,
        ]);

        $this->assertNull($subject->write(null, null));
    }

    public function testWrite_value()
    {
        $subject = $this->createSubject('test');

        $this->assertNotNull($subject->write(null, 'now'));
    }

    public function testSerialize_nullable()
    {
        $subject = $this->createSubject('test', [
            'nullable' => true,
        ]);

        $this->assertNull($subject->serialize(null, 'old', null));
    }

    public function testSerialize_default()
    {
        $default = new StdClass();
        $subject = $this->createSubject('test', [
            'default' => $default,
            'nullable' => false,
        ]);

        $this->assertEquals($default, $subject->serialize(null, 'old', null));
    }

    public function testSerialize_default_callable()
    {
        $default = new StdClass();
        $subject = $this->createSubject('test', [
            'nullable' => false,
            'default' => function () use ($default) {
                return $default;
            },
        ]);

        $this->assertSame($default, $subject->serialize(null, 'old', null));
    }

    public function testSerialize_value()
    {
        $subject = $this->createSubject('test');

        $this->assertNotNull($subject->serialize(null, 'old', 'now'));
    }

    public function testDeserialize_nullable()
    {
        $subject = $this->createSubject('test', [
            'nullable' => true,
        ]);

        $this->assertNull($subject->deserialize(null, null));
    }

    public function testDeserialize_value()
    {
        $subject = $this->createSubject('test');

        $this->assertNotNull($subject->deserialize(null, 'now'));
    }

    abstract public function createSubject($name, $opts = []);
}
