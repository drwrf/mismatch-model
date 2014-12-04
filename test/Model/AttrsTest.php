<?php

namespace Mismatch\Model;

use Mockery;

class AttrsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->metadata = Mockery::mock('Mismatch\Metadata');
        $this->metadata->shouldReceive('getClass')
            ->andReturn('User');

        $this->subject = new Attrs($this->metadata);
        $this->subject->set('integer', 'integer');
        $this->subject->set('set', 'set[integer]');
    }

    /**
     * @expectedException Mismatch\Model\Exception\InvalidAttrException
     */
    public function test_get_invalidAttr()
    {
        $this->subject->get('invalid');
    }

    public function test_has_invalidAttr()
    {
        $this->assertFalse($this->subject->has('invalid'));
    }

    public function test_has_validAttr()
    {
        $this->assertTrue($this->subject->has('integer'));
    }

    public function test_get_validAttr()
    {
        $attr = $this->subject->get('integer');

        $this->assertInstanceOf('Mismatch\Model\Attr\Integer', $attr);
    }

    public function test_get_setsName()
    {
        $attr = $this->subject->get('integer');
        $this->assertEquals('integer', $attr->name);
        $this->assertEquals('integer', $attr->key);
    }

    public function test_get_remainsCached()
    {
        $attr1 = $this->subject->get('integer');
        $attr2 = $this->subject->get('integer');

        $this->assertInstanceOf('Mismatch\Model\Attr\Integer', $attr1);
        $this->assertInstanceOf('Mismatch\Model\Attr\Integer', $attr2);
        $this->assertSame($attr1, $attr2);
    }

    public function test_get_dashedName()
    {
        $mock = Mockery::mock('Mismatch\Model\Attr\AttrInterface');

        Attrs::registerType('dashed-name', function() use ($mock) {
            return $mock;
        });

        $this->subject->set('dashed', 'dashed-name');
        $this->assertSame($mock, $this->subject->get('dashed'));
    }

    public function test_get_typeCallback()
    {
        $mock = Mockery::mock('Mismatch\Model\Attr\AttrInterface');

        Attrs::registerType('callback', function() use ($mock) {
            return $mock;
        });

        $this->subject->set('callback', 'callback');
        $this->assertSame($mock, $this->subject->get('callback'));
    }
}
