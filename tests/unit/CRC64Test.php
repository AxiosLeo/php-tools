<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\CRC64;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CRC64Test extends TestCase
{
    public function testCRC64()
    {
        $crc64 = new CRC64();
        $crc64->append('t');
        $first_val = $crc64->value();
        $this->assertEquals('9684896945377528848', $crc64->result());
        $crc64->append('e');
        $this->assertEquals('3877460061917441331', $crc64->result());
        $crc64->append('s');
        $this->assertEquals('8876108026412655253', $crc64->result());
        $crc64->append('t');
        $this->assertEquals('18020588380933092773', $crc64->result());

        $crc64->value($first_val);
        $crc64->append('e');
        $this->assertEquals('3877460061917441331', $crc64->result());
    }
}
