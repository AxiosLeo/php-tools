<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\ListToTree;
use axios\tools\TreeToList;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ListTreeTest extends TestCase
{
    public function testListToTree()
    {
        $data       = [
            ['id' => 1, 'parent_id' => 0],
            ['id' => 2, 'parent_id' => 3],
            ['id' => 3, 'parent_id' => 1],
            ['id' => 4, 'parent_id' => 2],
            ['id' => 5, 'parent_id' => 6],
            ['id' => 6, 'parent_id' => 7],
            ['id' => 7, 'parent_id' => 5],
        ];
        $ListToTree = new ListToTree($data);
        $this->assertEquals([
            [
                'id'        => 1,
                'parent_id' => 0,
                'child'     => [
                    0 => [
                        'id'        => 3,
                        'parent_id' => 1,
                        'child'     => [
                            0 => [
                                'id'        => 2,
                                'parent_id' => 3,
                                'child'     => [
                                    0 => [
                                        'id'        => 4,
                                        'parent_id' => 2,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $ListToTree->toTree());
    }

    public function testTreeToList()
    {
        $data       = [
            [
                'id'        => 1,
                'parent_id' => 0,
                'child'     => [
                    0 => [
                        'id'        => 3,
                        'parent_id' => 1,
                        'child'     => [
                            0 => [
                                'id'        => 2,
                                'parent_id' => 3,
                                'child'     => [
                                    0 => [
                                        'id'        => 4,
                                        'parent_id' => 2,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $TreeToList = new TreeToList($data);
        $this->assertEquals([
            ['id' => 1, 'parent_id' => 0],
            ['id' => 2, 'parent_id' => 1],
            ['id' => 3, 'parent_id' => 2],
            ['id' => 4, 'parent_id' => 3],
        ], $TreeToList->toList());
    }
}
