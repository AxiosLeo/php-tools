<?php

declare(strict_types=1);

namespace tpr\tools;

class NumberSystemConvert
{
    private $dict;
    private $patch;
    private $min_length;

    public function __construct(
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', // len:62
        $patch = '0',
        $min_length = null
    ) {
        $this->dict       = $dict;
        $this->patch      = $patch;
        $this->min_length = $min_length;
    }

    /**
     * @param int|string $num_str
     * @param int        $from       number system
     * @param int        $to         number system
     * @param null       $min_length
     *
     * @return string
     */
    public function anyToAny($num_str, $from, $to, $min_length = null)
    {
        if (\is_string($num_str)) {
            $num_str = (string) $num_str;
        }
        if (10 !== $from) {
            $fromBase = $this->anyToDecimal($num_str, $from);
        } else {
            $fromBase = $num_str;
        }

        $result = $this->decimalToAny($fromBase, $to);

        if (null !== $min_length) {
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
    public function anyToDecimal($num, $from)
    {
        $num  = (string) $num;
        $dict = $this->dict;
        $len  = \strlen($num);
        $dec  = 0;
        for ($i = 0; $i < $len; ++$i) {
            $pos = strpos($dict, $num[$i]);
            $dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
        }

        return $dec;
    }

    /**
     * @param string $num
     * @param int    $to  number_system
     *
     * @return string
     */
    public function decimalToAny($num, $to)
    {
        $dict = $this->dict;
        $ret  = '';
        do {
            $ret = $dict[bcmod($num, $to)] . $ret;
            $num = bcdiv($num, $to);
        } while ($num > 0);

        return $ret;
    }
}
