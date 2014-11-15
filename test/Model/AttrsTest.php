<?php

namespace Mismatch\Model;

use Mockery;

class AttrsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->metadata = Mockery::mock('Mismatch\Metadata');
        $this->subject = new Attrs($this->metadata);
        $this->subject->set('integer', 'Integer');
        $this->subject->set('set', 'Set[Integer]');
    }

    /**
     * @expectedException InvalidArgumentException
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

    public function test_get_typeCallback()
    {
        Attrs::registerType('Callback', function() {
            return 'worked!';
        });

        $this->subject->set('callback', 'Callback');
        $this->assertEquals('worked!', $this->subject->get('callback'));
    }
}
