<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\CDKEYProducer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CDKEYTest extends TestCase
{
    public function testProduceCDKEYWithZeroTicket()
    {
        $producer = new CDKEYProducer(0, 0);
        $this->assertEmpty($producer->get(100));
        $this->assertEmpty($producer->getOne());
    }

    public function testProduceCDKEYWithEmptyMix()
    {
        $producer = new CDKEYProducer(4, 0);
        $this->assertCount(100, $producer->get(100));
        $this->assertEquals('101D', $producer->getOne());
        $this->assertEquals('101E', $producer->getOne());
    }

    public function testProduceCDKEY()
    {
        $producer = new CDKEYProducer(4, 4);
        $this->assertCount(100, $producer->get(100));
        $this->assertTrue(0 === strpos($producer->getOne(), '101D'));
        $this->assertTrue(0 === strpos($producer->getOne(), '101E'));
    }
}
