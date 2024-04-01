<?php

declare(strict_types=1);

namespace axios\tools;

class BHDConverter
{
    private $dict;
    private $patch;

    public function __construct(
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', // len:62
        $patch = '0',
    ) {
        $this->dict  = $dict;
        $this->patch = $patch;
    }

    /**
     * @param int|string $num_str
     * @param int        $from       number system
     * @param int        $to         number system
     * @param int        $min_length default 0
     */
    public function anyToAny($num_str, int $from, int $to, int $min_length = 0): string
    {
        if (!\is_string($num_str)) {
            $num_str = (string) $num_str;
        }
        if (10 !== $from) {
            $fromBase = $this->anyToDecimal($num_str, $from);
        } else {
            $fromBase = $num_str;
        }

        $result = $this->decimalToAny($fromBase, $to);

        if (0 !== $min_length) {
            $strLength = \strlen($result);
            while ($strLength < $min_length) {
                $result = $this->patch . $result;
                ++$strLength;
            }
        }

        return $result;
    }

    /**
     * @param int|string $num
     * @param int        $from number_system : 10(Decimal) | 16(Hex) | 62(62 binary)
     *
     * @return int|string
     */
    public function anyToDecimal($num, int $from)
    {
        $num  = (string) $num;
        $from = (string) $from;
        $dict = $this->dict;
        $len  = \strlen($num);
        $dec  = '0';
        for ($i = 0; $i < $len; ++$i) {
            $pos = strpos($dict, $num[$i]);
            $dec = bcadd(bcmul(bcpow($from, (string) ($len - $i - 1)), (string) $pos), $dec);
        }

        return $dec;
    }

    /**
     * @param int|string $num
     * @param int        $to  number_system
     */
    public function decimalToAny($num, int $to): string
    {
        $num  = (string) $num;
        $to   = (string) $to;
        $dict = $this->dict;
        $ret  = '';
        do {
            $ret = $dict[bcmod($num, $to)] . $ret;
            $num = bcdiv($num, $to);
        } while ($num > 0);

        return $ret;
    }
}
