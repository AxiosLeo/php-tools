<?php

declare(strict_types=1);

namespace axios\tools;

class MemoryCounter
{
    private $group;
    private $name;
    private $size;
    private $id;

    public function __construct(string $group = __FILE__, string $name = 'p', $size = 8)
    {
        $this->group = $group;
        $this->name  = $name;
        $this->size  = $size;
    }

    public function config($config = [])
    {
        foreach ($config as $key => $val) {
            if (isset($this->{$key})) {
                $this->{$key} = $val;
                $this->id     = null;
            }
        }

        return get_object_vars($this);
    }

    public function id()
    {
        if (null === $this->id) {
            throw new \ErrorException('Have not used the create() method to create a counter');
        }

        return $this->id;
    }

    /**
     * @param int $ini
     *
     * @return $this
     */
    public function create($ini = 0): self
    {
        $shm      = ftok($this->group, $this->name);
        $this->id = shmop_open($shm, 'c', 0644, $this->size);
        $this->set($ini);

        return $this;
    }

    /**
     * @param int $step
     *
     * @return int return current value of counter
     */
    public function increase($step = 1): int
    {
        $curr = $this->current();
        $curr = $curr + $step;
        $this->set($curr);

        return (int) $curr;
    }

    /**
     * @param int $step
     *
     * @return int return current value of counter
     */
    public function decrease($step = 1): int
    {
        $curr = $this->current();
        $curr = $curr - $step;
        $this->set($curr);

        return (int) $curr;
    }

    public function current(): int
    {
        $current = shmop_read($this->id(), 0, $this->size);

        return empty($current) ? 0 : (int) $current;
    }

    public function set($val): void
    {
        $val = str_pad((string) $val, $this->size, '0', STR_PAD_LEFT);
        shmop_write($this->id(), $val, 0);
    }

    public function clear(): void
    {
        shmop_close($this->id());
        shmop_delete($this->id());
        $this->id = null;
    }
}
