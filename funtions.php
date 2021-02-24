<?php

declare(strict_types=1);

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

if (!function_exists('halt') && function_exists('dump')) {
    function halt(...$params)
    {
        dump(...$params);

        exit();
    }
}

if (!function_exists('client_ip')) {
    /*
     * get the IP address of the client
     *
     * @param int  $type 0 => return IP string; 1=> return IP number
     * @param bool $adv  advance mode
     *
     * @return mixed
     */
    function client_ip($type = 0, $adv = false)
    {
        $type              = $type ? 1 : 0;
        static $ip         = null;
        if (null !== $ip) {
            return $ip[$type];
        }
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP address legitimacy verification
        $long = sprintf('%u', ip2long($ip));
        $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];

        return $ip[$type];
    }
}
