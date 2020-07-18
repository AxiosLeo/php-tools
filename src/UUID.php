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

    public function v3($cut = 8, $flavour = '-', $isUpper = false)
    {
        $str    = self::v2();
        $len    = \strlen($str);
        $length = $len;
        $uuid   = '';
        if (\is_array($cut)) {
            while ($length > 0) {
                $uuid .= substr($str, $len - $length, array_rand($cut)) . $flavour;
                $length -= $cut;
            }
        } elseif (\is_int($cut)) {
            $step = 0;
            while ($length > 0) {
                $temp   = substr($str, $len - $length, $cut);
                $uuid .= 0 != $step ? $flavour . $temp : $temp;
                $length -= $cut;
                ++$step;
            }
        }

        return $isUpper ? strtoupper($uuid) : $uuid;
    }
}
