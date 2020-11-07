<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\XMLParser as XML;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class XMLParserTest extends TestCase
{
    public function test()
    {
        $data     = [
            'foo'  => 'bar',
            'bool' => true,
            'list' => [1, 2, 3, 4],
            'map'  => ['a' => 'b', 'c' => 'd'],
        ];
        $expected = '<?xml version="1.0" encoding="utf-8"?><data><foo>bar</foo><bool>1</bool><list><item id="0">1</item><item id="1">2</item><item id="2">3</item><item id="3">4</item></list><map><a>b</a><c>d</c></map></data>';
        $this->assertEquals(
            $expected,
            XML::encode($data)
        );
        $this->assertEquals([
            'foo'  => 'bar',
            'bool' => true,
            'list' => [
                'item' => [1, 2, 3, 4],
            ],
            'map'  => ['a' => 'b', 'c' => 'd'],
        ], XML::decode($expected));
    }
}
