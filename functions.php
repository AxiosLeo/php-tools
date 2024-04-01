<?php

declare(strict_types=1);

use axios\tools\HMac;
use axios\tools\Path;
use axios\tools\SM3;
use axios\tools\UUID;
use axios\tools\XMLParser;

if (!function_exists('render_str')) {
    function render_str(string $template, array $params, string $left_tag = '${', string $right_tag = '}'): string
    {
        foreach ($params as $name => $value) {
            $template = str_replace($left_tag . $name . $right_tag, $value, $template);
        }

        return $template;
    }
}

if (!function_exists('exec_command')) {
    function exec_command($cmd, ?string $cwd = null)
    {
        if (null !== $cwd) {
            $cmd = 'cd ' . $cwd . ' && ' . $cmd;
        }
        while (@ob_end_flush()) {
            continue;
        } // end all output buffers if any
        $proc = popen($cmd, 'r');
        while (!feof($proc)) {
            echo fread($proc, 4096);
            @flush();
        }
    }
}

if (!function_exists('hmac')) {
    function hmac(string $algorithm, string $data = '', string $secret = '', bool $raw_output = false): string
    {
        $hamc = new HMac();
        $res  = $hamc->count($algorithm, $data, $secret, $raw_output);
        unset($hamc);

        return $res;
    }
}

if (!function_exists('halt') && function_exists('dump')) {
    function halt(...$args)
    {
        dump(...$args);

        exit();
    }
}

if (!function_exists('sm3')) {
    function sm3(string $str, bool $raw_output = false): string
    {
        $sm3 = new SM3();
        $sm3->encode($str);

        return $raw_output ? $sm3->getBinary() : $sm3->getHex();
    }
}

if (!function_exists('sm3_file')) {
    function sm3_file(string $filepath, bool $raw_output = false): string
    {
        $sm3 = new SM3();
        $sm3->encodeFile($filepath);

        return $raw_output ? $sm3->getBinary() : $sm3->getHex();
    }
}

if (!function_exists('xml_encode')) {
    function xml_encode(array $data, $root_node = 'data', $root_attr = [], $item_node = 'item', $item_key = 'id', $encoding = 'utf-8'): string
    {
        return XMLParser::encode($data, $root_node, $root_attr, $item_node, $item_key, $encoding);
    }
}

if (!function_exists('xml_decode')) {
    function xml_decode(string $xml_string): array
    {
        return XMLParser::decode($xml_string);
    }
}

if (!function_exists('uuid')) {
    function uuid(string $salt = ''): string
    {
        $uuid = new UUID($salt);
        $str  = $uuid->v2();
        unset($uuid);

        return $str;
    }
}

if (!function_exists('path_join')) {
    function path_join(string ...$paths): string
    {
        return Path::join(...$paths);
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
    function client_ip(int $type = 0, bool $advance = false)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }
        if ($advance) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip  = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP address legitimacy verification
        $long      = sprintf('%u', ip2long((string) $ip));
        $ip        = $long ? [$ip, $long] : ['0.0.0.0', 0];

        return $ip[$type];
    }
}
