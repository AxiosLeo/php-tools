<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\Datetime;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DatetimeTest extends TestCase
{
    private $datetime;

    public function setUp(): void
    {
        parent::setUp();
        $this->datetime = strtotime('2020-06-06 23:12:46');
    }

    public function testHourBeginEnd()
    {
        $obj               = new Datetime($this->datetime);
        list($begin, $end) = $obj->hourBeginEnd(12);
        $this->assertEquals(1591444800, $begin);
        $this->assertEquals(1591448399, $end);
        $this->assertEquals(3599, $end - $begin);
    }

    public function testDayBeginEnd()
    {
        $obj               = new Datetime($this->datetime);
        list($begin, $end) = $obj->dayBeginEnd('2020-06-06');
        $this->assertEquals(1591401600, $begin);
        $this->assertEquals(1591487999, $end);
        $this->assertEquals(86399, $end - $begin);
    }

    public function testMonthBeginEnd()
    {
        $obj               = new Datetime($this->datetime);
        list($begin, $end) = $obj->monthBeginEnd('2020', '6');
        $this->assertEquals(1590969600, $begin);
        $this->assertEquals(1593561599, $end);
        $this->assertEquals(2591999, $end - $begin);
    }
}
