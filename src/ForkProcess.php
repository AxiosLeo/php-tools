<?php

declare(strict_types=1);

namespace axios\tools;

class ForkProcess
{
    use InstanceTrait;

    private $work_queue = [];
    private $max_process;

    public function __construct($max_process = 100)
    {
        $this->max_process = $max_process;
        $check_funcs       = [
            'pcntl_fork',
            'posix_kill',
            'ftok',
            'shmop_open',
        ];
        foreach ($check_funcs as $func) {
            if (\function_exists($func)) {
                continue;
            }

            throw new \ErrorException($func . '() function is undefined.');
        }
    }

    public function maxProcess($max_process = null)
    {
        if (null === $max_process) {
            $this->max_process = $max_process;
        }

        return $this->max_process;
    }

    public function addWork($class, $func, $args = [])
    {
        $queue = [
            'class' => $class,
            'func'  => $func,
            'args'  => $args,
        ];
        array_push($this->work_queue, $queue);

        return $this;
    }

    public function run()
    {
        foreach ($this->work_queue as $q) {
            do {
                $pid_size = shell_exec('ps -fe |grep "php-fpm"|grep "pool"|wc -l');
            } while ($pid_size >= $this->max_process);
            $this->exec($q);
        }
    }

    private function exec($queue)
    {
        $class = $queue['class'];
        $func  = $queue['func'];
        $args  = $queue['args'];
        if (\is_string($class) && class_exists($class)) {
            $class = new $class();
        }
        $fork = $this->fork();
        if ($fork) {
            return $fork;
        }
        \call_user_func_array([$class, $func], $args);
        posix_kill(posix_getpid(), SIGINT);
        exit();
    }

    private function fork(): bool
    {
        $pid = pcntl_fork();
        if ($pid > 0) {
            pcntl_wait($status);

            return true;
        }
        if (0 === $pid) {
            $ppid = pcntl_fork();
            if ($ppid > 0) {
                posix_kill(posix_getpid(), SIGINT);
                exit();
            }
            if (-1 == $ppid) {
                exit();
            }

            return false;
        }

        return false;
    }
}
