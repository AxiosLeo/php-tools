<?php

declare(strict_types=1);

namespace axios\tools;

use axios\tools\traits\CallPropTrait;

/**
 * Class TreeToList.
 *
 * @method string parent_index($parent_index = null)
 * @method string node_index($node_index = null)
 * @method string node_name($node_name = null)
 * @method string layer_name($layer_name = null)
 */
class TreeToList
{
    use CallPropTrait;

    private array $tree;

    private string $parent_index = 'parent_id';

    private string $node_index = 'id';

    private string $node_name = 'child';

    private string $layer_name = '';

    private int $count;

    public function __construct(array $tree = [])
    {
        $this->tree = $tree;
    }

    public function toList(): array
    {
        $this->count = 0;
        $this->recurse($data, $this->tree);

        return $data;
    }

    private function recurse(?array &$data = [], array $tree = [], int $layer = 0, int $parent_id = 0): void
    {
        foreach ($tree as $t) {
            ++$this->count;
            $node                      = $t;
            $node[$this->node_index]   = $this->count;
            $node[$this->parent_index] = $parent_id;
            unset($node[$this->node_name]);
            if ($this->layer_name !== '') {
                $node[$this->layer_name] = $layer;
            }
            $data[] = $node;
            if (isset($t[$this->node_name]) && !empty($t[$this->node_name])) {
                $this->recurse($data, $t[$this->node_name], $layer + 1, $this->count);
            }
        }
    }
}
