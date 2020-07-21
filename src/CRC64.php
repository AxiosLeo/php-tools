<?php

declare(strict_types=1);

namespace axios\tools;

class CRC64
{
    private static $crc64tab;

    private $value = 0;

    public function __construct()
    {
        if (null === self::$crc64tab) {
            $crc64tab  = [];
            $poly64rev = (0xC96C5795 << 32) | 0xD7870F42;
            for ($n = 0; $n < 256; ++$n) {
                $crc = $n;
                for ($k = 0; $k < 8; ++$k) {
                    if ($crc & true) {
                        $crc = ($crc >> 1) & ~(0x8 << 60) ^ $poly64rev;
                    } else {
                        $crc = ($crc >> 1) & ~(0x8 << 60);
                    }
                }
                $crc64tab[$n] = $crc;
            }
            self::$crc64tab = $crc64tab;
        }
    }

    public function append($string)
    {
        for ($i = 0; $i < \strlen($string); ++$i) {
            $this->value = ~$this->value;
            $this->value = $this->count(\ord($string[$i]), $this->value);
            $this->value = ~$this->value;
        }
    }

    public function value()
    {
        return (string) (sprintf('%u', $this->value));
    }

    private function count($byte, $crc)
    {
        return self::$crc64tab[($crc ^ $byte) & 0xff] ^ (($crc >> 8) & ~(0xff << 56));
    }
}
