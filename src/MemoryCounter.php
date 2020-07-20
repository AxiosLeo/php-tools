<?php

declare(strict_types=1);

namespace axios\tools;

class MemoryCounter
{
    private $group;
    private $name;
    private $size;

    public function __construct(string $group, string $name, $size = 8)
    {
        $this->group = $group;
        $this->name  = $name;
        $this->size  = $size;
    }

    /**
     * @param array $config
     */
    public function config($config = [])
    {
        foreach ($config as $key => $val) {
            if (isset($this->{$key})) {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * @return resource
     */
    public function id()
    {
        $shm = ftok($this->group, $this->name);

        return shmop_open($shm, 'c', 0644, $this->size);
    }

    /**
     * @param int $ini
     *
     * @return int
     */
    public function create($ini = 0): int
    {
        $this->set($ini);

        return $ini;
    }

    /**
     * @param int $step
     *
     * @return int
     */
    public function increase($step = 1): int
    {
        $curr = $this->current();
        $curr = $curr + $step;
        $this->set($curr + $step);

        return (int) $curr;
    }

    /**
     * @param int $step
     *
     * @return int
     */
    public function decrease($step = 1): int
    {
        $curr = $this->current();
        $this->set($curr - $step);

        return (int) $curr;
    }

    /**
     * @return int
     */
    public function current(): int
    {
        $current = shmop_read($this->id(), 0, $this->size);

        return empty($current) ? 0 : (int) $current;
    }

    /**
     * @param $val
     */
    public function set($val): void
    {
        $val = str_pad($val, $this->size, '0', STR_PAD_LEFT);
        shmop_write($this->id(), $val, 0);
    }
}
