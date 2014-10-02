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
}
