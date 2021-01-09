<?php

declare (strict_types = 1);

use axios\tools\HMac;

if (!function_exists('hmac')) {
    function hmac($algorithm, $data = null, $secret = null, bool $raw_output = false)
    {
        $hamc = new HMac();
        $res  = $hamc->count($algorithm, $data, $secret, $raw_output);
        unset($hamc);
        return $res;
    }
}