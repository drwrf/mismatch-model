<?php

namespace Mismatch\Model\Attr;

use Mockery;

class PrimaryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->subject = new Primary('id', [
            'each' => Mockery::mock('Mismatch\Model\Attr\Integer')
        ]);
    }

    public function testRead_castsToChild()
    {
        $this->subject->each
            ->shouldReceive('cast')
            ->with('1')
            ->andReturn(1);

        $this->assertEquals(1, $this->subject->read(null, '1'));
    }

    public function testRead_buildEach()
    {
        $subject = new Primary('id', [
            'each' => 'Mismatch\Model\Attr\Integer',
        ]);

        // Indirect testing of this method is okay with me
        $this->assertEquals(1, $subject->read(null, '1'));
    }
}
