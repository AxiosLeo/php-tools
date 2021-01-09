<?php

declare(strict_types=1);

namespace axios\tools;

class HMac
{
    public $algos = [];

    public function count(string $algorithm, $data = null, $secret = null, bool $raw_output = false): string
    {
        if (\in_array($algorithm, hash_algos())) {
            return hash_hmac($algorithm, $data, $secret, $raw_output);
        }
        if (!isset($this->algos[$algorithm])) {
            throw new \RuntimeException('Unsupported algorithm: ' . $algorithm);
        }
        $callback = $this->algos[$algorithm];
        $size     = \strlen($callback('test'));
        $pack     = 'H' . (string) $size;
        if (\strlen($secret) > $size) {
            $secret = pack($pack, $callback($secret));
        }
        $key  = str_pad($secret, $size, \chr(0x00));
        $ipad = $key ^ str_repeat(\chr(0x36), $size);
        $opad = $key ^ str_repeat(\chr(0x5C), $size);
        $hmac = $callback($opad . pack($pack, $callback($ipad . $data)));

        return $raw_output ? pack($pack, $hmac) : $hmac;
    }

    public function registerAlgorithm($algorithm_name, \Closure $callback)
    {
        $this->algos[$algorithm_name] = $callback;
    }
}
