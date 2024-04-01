<?php

declare(strict_types=1);

namespace axios\tools;

/**
 * Class SM3.
 *
 * @reference https://github.com/lizhichao/sm
 */
class SM3
{
    public const IV         = '7380166f4914b2b9172442d7da8a0600a96f30bc163138aae38dee4db0fb0e4e';
    public const LEN        = 512;
    public const STR_LEN    = 64;

    private ?string $hex    = null;
    private ?string $binary = null;
    private ?string $base64 = null;

    public function encode(string $str): self
    {
        $l            = strlen($str) * 8;
        $k            = $this->getK($l);
        $bt           = $this->getB($k);
        $str          = $str . $bt . pack('J', $l);

        $count        = strlen($str);
        $l            = $count / self::STR_LEN;
        $vr           = hex2bin(self::IV);
        for ($i = 0; $i < $l; ++$i) {
            $vr = $this->cf($vr, substr($str, $i * self::STR_LEN, self::STR_LEN));
        }
        $this->binary = $vr;

        return $this;
    }

    public function encodeFile(string $file): self
    {
        $l            = filesize($file) * 8;
        $k            = $this->getK($l);
        $bt           = $this->getB($k) . pack('J', $l);

        $hd           = fopen($file, 'r');
        $vr           = hex2bin(self::IV);
        $str          = fread($hd, self::STR_LEN);
        if ($l > self::LEN - self::STR_LEN - 1) {
            do {
                $vr  = $this->cf($vr, $str);
                $str = fread($hd, self::STR_LEN);
            } while (!feof($hd));
        }

        $str .= $bt;
        $count        = strlen($str)    * 8;
        $l            = $count / self::LEN;
        for ($i = 0; $i < $l; ++$i) {
            $vr = $this->cf($vr, substr($str, $i * self::STR_LEN, self::STR_LEN));
        }
        $this->binary = $vr;

        return $this;
    }

    public function getBinary(): string
    {
        return $this->binary;
    }

    public function getBase64(): string
    {
        if (null === $this->base64) {
            $this->base64 = base64_encode($this->binary);
        }

        return $this->base64;
    }

    public function getHex(): string
    {
        if (null === $this->hex) {
            $this->hex = bin2hex($this->getBinary());
        }

        return $this->hex;
    }

    private function getK($l): int
    {
        $v = $l % self::LEN;

        return $v + self::STR_LEN < self::LEN
            ? self::LEN       - self::STR_LEN - $v - 1
            : (self::LEN * 2) - self::STR_LEN - $v - 1;
    }

    private function getB($k): string
    {
        $arg = [128];
        $arg = array_merge($arg, array_fill(0, (int) ($k / 8), 0));
        $res = pack('C*', ...$arg);
        if (false === $res) {
            $res = '';
        }

        return $res;
    }

    private function t($i): int
    {
        return $i < 16 ? 0x79CC4519 : 0x7A879D8A;
    }

    private function cf($ai, $bi)
    {
        $wr                                  = array_values(unpack('N*', $bi));
        for ($i = 16; $i < 68; ++$i) {
            $wr[$i] = $this->p1($wr[$i - 16]
                    ^ $wr[$i - 9]
                    ^ $this->lm($wr[$i - 3], 15))
                ^ $this->lm($wr[$i - 13], 7)
                ^ $wr[$i - 6];
        }
        $wr1                                 = [];
        for ($i = 0; $i < 64; ++$i) {
            $wr1[] = $wr[$i] ^ $wr[$i + 4];
        }

        list($a, $b, $c, $d, $e, $f, $g, $h) = array_values(unpack('N*', $ai));

        for ($i = 0; $i < 64; ++$i) {
            $ss1 = $this->lm(
                ($this->lm($a, 12) + $e + $this->lm($this->t($i), $i % 32) & 0xFFFFFFFF),
                7
            );
            $ss2 = $ss1 ^ $this->lm($a, 12);
            $tt1 = ($this->ff($i, $a, $b, $c) + $d + $ss2 + $wr1[$i]) & 0xFFFFFFFF;
            $tt2 = ($this->gg($i, $e, $f, $g) + $h + $ss1 + $wr[$i])  & 0xFFFFFFFF;
            $d   = $c;
            $c   = $this->lm($b, 9);
            $b   = $a;
            $a   = $tt1;
            $h   = $g;
            $g   = $this->lm($f, 19);
            $f   = $e;
            $e   = $this->p0($tt2);
        }

        return pack('N*', $a, $b, $c, $d, $e, $f, $g, $h) ^ $ai;
    }

    private function ff($j, $x, $y, $z): int
    {
        return $j < 16 ? $x ^ $y ^ $z : ($x & $y) | ($x & $z) | ($y & $z);
    }

    private function gg($j, $x, $y, $z): int
    {
        return $j < 16 ? $x ^ $y ^ $z : ($x & $y) | (~$x & $z);
    }

    private function lm($a, $n): int
    {
        return $a >> (32 - $n) | (($a << $n) & 0xFFFFFFFF);
    }

    private function p0($x): int
    {
        return $x ^ $this->lm($x, 9) ^ $this->lm($x, 17);
    }

    private function p1($x): int
    {
        return $x ^ $this->lm($x, 15) ^ $this->lm($x, 23);
    }
}
