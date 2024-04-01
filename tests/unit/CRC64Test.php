<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\CRC64;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CRC64Test extends TestCase
{
    public function testCRC64()
    {
        $crc64     = new CRC64();
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

    public function testWithChinese()
    {
        $crc64     = new CRC64();
        $crc64->append('中');
        $this->assertEquals('5878784518235156503', $crc64->result());
        $first_val = $crc64->value();
        $crc64->append('文');
        $this->assertEquals('16371802884590399230', $crc64->result());
        $crc64->append('内');
        $this->assertEquals('14919037537769559253', $crc64->result());
        $crc64->append('容');
        $this->assertEquals('16369845890586830377', $crc64->result());

        $crc64->value($first_val);
        $crc64->append('文');
        $this->assertEquals('16371802884590399230', $crc64->result());
    }
}
