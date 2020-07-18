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

    public function v0()
    {
        return uniqid(microtime(true));
    }

    public function v1()
    {
        return uniqid(md5(microtime(true)));
    }

    public function v2()
    {
        return md5($this->salt . uniqid(md5(microtime(true)), true));
    }

    public function v3($cut = 8, $flavour = '-')
    {
        $str    = self::v2();
        $length = 32;
        $tmp    = [];
        while ($length > 0) {
            $part = substr($str, 32 - $length, $cut);
            array_push($tmp, $part);
            $length -= $cut;
        }

        return implode($flavour, $tmp);
    }

    public function v4($cut = [6, 7, 9, 10], $flavour = '-')
    {
        array_sum($cut);
        if (array_sum($cut) < 32) {
            throw new \InvalidArgumentException('Invalid cut part length');
        }
        $str    = self::v2();
        $length = 32;
        $tmp    = [];
        while ($length > 0) {
            $part = substr($str, 32 - $length, array_rand($cut));
            array_push($tmp, $part);
            $length -= $cut;
        }

        return implode($flavour, $tmp);
    }
}
