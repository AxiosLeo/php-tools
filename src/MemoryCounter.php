<?php

declare (strict_types = 1);

namespace tpr\tools;

class MemoryCounter
{
    private $pathname;
    private $type;
    private $size;

    public function __construct($pathname, $type, $size = 8)
    {
        $this->pathname = $pathname;
        $this->type     = $type;
        $this->size     = $size;
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

    public function create($ini = 0)
    {
        $shm    = ftok($this->pathname, $this->type);
        $shm_id = shmop_open($shm, 'c', 0644, $this->size);
        $curr   = $ini;
        $curr   = str_pad($curr, $this->size, '0', STR_PAD_LEFT);
        shmop_write($shm_id, $curr, 0);

        return $curr;
    }

    public function increase($step = 1)
    {
        $shm    = ftok($this->pathname, $this->type);
        $shm_id = shmop_open($shm, 'c', 0644, $this->size);
        $curr   = shmop_read($shm_id, $this->size, $this->size);
        $curr   = empty($curr) ? 1 : (int)$curr;
        $curr   = $curr + $step;
        $curr   = str_pad($curr, $this->size, '0', STR_PAD_LEFT);
        shmop_write($shm_id, $curr, 0);

        return $curr;
    }

    public function decrease($step = 1)
    {
        $shm    = ftok($this->pathname, $this->type);
        $shm_id = shmop_open($shm, 'c', 0644, $this->size);
        $curr   = shmop_read($shm_id, 0, $this->size);
        $curr   = $curr - $step;
        $curr   = str_pad($curr, $this->size, '0', STR_PAD_LEFT);
        shmop_write($shm_id, $curr, 0);
        return $curr;
    }

    public function current()
    {
        $shm     = ftok($this->pathname, $this->type);
        $shm_id  = shmop_open($shm, 'c', 0644, $this->size);
        $current = shmop_read($shm_id, 0, $this->size);

        return empty($current) ? 0 : (int)$current;
    }
}
