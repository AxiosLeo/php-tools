<?php

declare(strict_types=1);

namespace axios\tools;

class UUID
{
    private $salt;

    public function __construct($salt = '')
    {
        $this->salt = $salt;
    }

    public function v0(): string
    {
        return uniqid((string) (microtime(true)));
    }

    public function v1(): string
    {
        return uniqid(md5((string) (microtime(true))));
    }

    public function v2(): string
    {
        return md5($this->salt . uniqid(md5((string) (microtime(true))), true));
    }

    public function v3($cut = 8, $flavour = '-'): string
    {
        $str    = $this->v2();
        $length = 32;
        $tmp    = [];
        while ($length > 0) {
            $part  = substr($str, 32 - $length, $cut);
            $tmp[] = $part;
            $length -= $cut;
        }

        return implode($flavour, $tmp);
    }

    public function v4($cut = [6, 7, 9, 10], $flavour = '-'): string
    {
        $str    = $this->v2();
        $length = 32;
        $tmp    = [];
        while ($length > 0) {
            $cut_val = array_rand($cut);
            $part    = substr($str, 32 - $length, $cut_val);
            $tmp[]   = $part;
            $length  = $length - $cut_val;
        }

        return implode($flavour, $tmp);
    }
}
