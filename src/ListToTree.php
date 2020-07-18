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

    private $list;

    private $parent_index = 'parent_id';

    private $node_index = 'id';

    private $node_name = 'child';

    public function __construct($list = [])
    {
        $this->list = $list;
    }

    public function tree()
    {
        $items = [];
        $data  = $this->list;
        foreach ($data as $d) {
            $items[$d[$this->node_index]] = $d;
            if (!isset($d[$this->parent_index]) || !isset($d[$this->node_index]) || isset($d[$this->node_name])) {
                return false;
            }
        }
        $tree = [];
        $n    = 0;
        foreach ($items as $item) {
            if (isset($items[$item[$this->parent_index]])) {
                $items[$item[$this->parent_index]][$this->node_name][] = &$items[$item[$this->node_index]];
            } else {
                $tree[$n++] = &$items[$item[$this->node_index]];
            }
        }

        return $tree;
    }
}
