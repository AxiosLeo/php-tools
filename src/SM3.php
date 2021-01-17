<?php

declare(strict_types=1);

namespace axios\tools;

/**
 * Class SM3.
 *
 * @reference https://www.fengkx.top/post/sm3-implementing/
 */
class SM3
{
    const IV = '7380166f4914b2b9172442d7da8a0600a96f30bc163138aae38dee4db0fb0e4e';

    private string $value = '';

    public function __construct($str)
    {
        $data = (string) $str;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getBase64Encode(): string
    {
        return base64_encode($this->value);
    }

    public function getHexEncode()
    {
        return bin2hex($this->value);
    }

    private function padding()
    {
    }
}
