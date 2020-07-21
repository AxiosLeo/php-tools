<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\UUID;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UUIDTest extends TestCase
{
    public function testUUID()
    {
        $uuid = new UUID();
        $uuid->v0();
        $uuid->v1();
        $uuid->v2();
        $uuid->v3();
        $uuid->v4();
        $this->assertTrue(true);
    }
}
