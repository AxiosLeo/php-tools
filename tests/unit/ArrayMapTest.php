<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\ArrayMap;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ArrayMapTest extends TestCase
{
    public function testSetGet()
    {
        $array = new ArrayMap();
        $array->set('0.test.a.0.b', 'test');
        $this->assertEquals('test', $array->get('0.test.a.0.b'));
    }

    public function testGetAllToString()
    {
        $array = new ArrayMap([
            'a' => null,
            'b' => 1,
            'c' => 0x0001,
            'd' => true,
        ]);
        $this->assertEquals([
            'a' => '',
            'b' => '1',
            'c' => '1',
            'd' => 'true',
        ], $array->getAllToString());
    }

    public function testDelete()
    {
        $array = new ArrayMap([
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
        ]);
        $array->delete('a');
        $this->assertEquals([
            'b' => 'b',
            'c' => 'c',
        ], $array->get());

        unset($array['b']);
        $this->assertEquals([
            'c' => 'c',
        ], $array->get());
    }
}
