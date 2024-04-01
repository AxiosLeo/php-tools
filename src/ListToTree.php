<?php

declare(strict_types=1);

namespace axios\tools;

use axios\tools\traits\CallPropTrait;

/**
 * Class ListToTree.
 *
 * @method string parent_index($parent_index = null)
 * @method string node_index($node_index = null)
 * @method string node_name($node_name = null)
 */
class ListToTree
{
    use CallPropTrait;

    private array $list;

    private string $parent_index = 'parent_id';

    private string $node_index   = 'id';

    private string $node_name    = 'child';

    public function __construct(array $list = [])
    {
        $this->list = $list;
    }

    public function toTree(): array
    {
        $items = [];
        $data  = $this->list;
        foreach ($data as $d) {
            $items[$d[$this->node_index]] = $d;
        }
        $tree  = [];
        $n     = 0;
        foreach ($items as $node_index => $item) {
            if (isset($items[$item[$this->parent_index]])) {
                $items[$item[$this->parent_index]][$this->node_name][] = &$items[$node_index];
            } else {
                $tree[$n++] = &$items[$item[$this->node_index]];
            }
        }

        return $tree;
    }
}
