<?php

declare(strict_types=1);

namespace axios\tools\traits;

trait CallPropTrait
{
    public function __call($name, $arguments)
    {
        if (isset($arguments[0]) && null !== $arguments) {
            $this->{$name} = $arguments[0];
        }

        return $this->{$name};
    }
}
