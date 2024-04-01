<?php

declare(strict_types=1);

namespace axios\tools;

class CRC64
{
    private static array $crc64tab = [];

    private int $value             = 0;

    public function __construct()
    {
        if ([] === self::$crc64tab) {
            $poly64rev = (0xC96C5795 << 32) | 0xD7870F42;
            for ($n = 0; $n < 256; ++$n) {
                $crc                = $n;
                for ($k = 0; $k < 8; ++$k) {
                    if ($crc & true) {
                        $crc = ($crc >> 1) & ~(0x8 << 60) ^ $poly64rev;
                    } else {
                        $crc = ($crc >> 1) & ~(0x8 << 60);
                    }
                }
                self::$crc64tab[$n] = $crc;
            }
        }
    }

    public function append($string): void
    {
        for ($i = 0; $i < \strlen($string); ++$i) {
            $this->value = ~$this->value;
            $this->value = $this->count(\ord($string[$i]), $this->value);
            $this->value = ~$this->value;
        }
    }

    public function value($value = null): int
    {
        if (null !== $value) {
            $this->value = $value;
        }

        return $this->value;
    }

    public function result(): string
    {
        return (string) (sprintf('%u', $this->value));
    }

    private function count($byte, $crc): int
    {
        return self::$crc64tab[($crc ^ $byte) & 0xFF] ^ (($crc >> 8) & ~(0xFF << 56));
    }
}
