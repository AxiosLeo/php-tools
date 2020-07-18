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

    private $tree;

    private $parent_index = 'parent_id';

    private $node_index = 'id';

    private $node_name = 'child';

    private $layer_name;

    private $count;

    public function __construct($tree = [])
    {
        $this->tree = $tree;
    }

    public function toList()
    {
        $this->count = 0;
        $this->recurse($data, $this->tree);

        return $data;
    }

    private function recurse(&$data = [], $tree = [], $layer = 0, $parent_id = 0)
    {
        foreach ($tree as $t) {
            ++$this->count;
            $node                      = $t;
            $node[$this->node_index]   = $this->count;
            $node[$this->parent_index] = $parent_id;
            unset($node[$this->node_name]);
            if (null !== $this->layer_name) {
                $node[$this->layer_name] = $layer;
            }
            $data[] = $node;
            if (isset($t[$this->node_name]) && !empty($t[$this->node_name])) {
                $this->recurse($data, $t[$this->node_name], $layer + 1, $this->count);
            }
        }
    }
}
