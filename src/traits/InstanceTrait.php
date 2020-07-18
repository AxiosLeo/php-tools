<?php

declare(strict_types=1);

namespace axios\tools\traits;

trait InstanceTrait
{
    private static $instance;

    public static function __callStatic($name, $arguments)
    {
        return \call_user_func_array([self::$instance, $name], $arguments);
    }

    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function clear(): void
    {
        self::$instance = null;
    }
}
