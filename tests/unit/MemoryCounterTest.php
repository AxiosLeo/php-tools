<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class MemoryCounterTest extends TestCase
{
    public function testCounterWithError()
    {
        $this->expectException(\ErrorException::class);
        $this->expectDeprecationMessage('Have not used the create() method to create a counter');
        $counter = new \axios\tools\MemoryCounter();
        $counter->current();
    }

    public function testCounter()
    {
        $counter = new \axios\tools\MemoryCounter();
        $counter->create();
        $this->assertEquals(0, $counter->current());
        $this->assertEquals(1, $counter->increase());
        $this->assertEquals(0, $counter->decrease());
    }
}
