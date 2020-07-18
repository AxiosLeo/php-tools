<?php

declare(strict_types=1);

namespace axios\tools;

class Helper
{
    /**
     * @param        $date
     * @param        $hour
     * @param string $format
     *
     * @return array
     *
     * @deprecated use Datetime instead, pls
     */
    public static function getHourBeginEndTime($date, $hour, $format = 'timestamp')
    {
        $datetime          = new Datetime(strtotime($date));
        list($begin, $end) = $datetime->hourBeginEnd($hour);
        if ('timestamp' == $format) {
            return [
                'begin' => $begin,
                'end'   => $end,
            ];
        }

        return [
            'begin' => date($format, $begin),
            'end'   => date($format, $end),
        ];
    }

    /**
     * @param        $date
     * @param string $format
     *
     * @return array
     *
     * @deprecated use Datetime instead, pls
     */
    public static function getDayBeginEndTime($date, $format = 'timestamp')
    {
        $datetime          = new Datetime();
        list($begin, $end) = $datetime->dayBeginEnd($date);
        if ('timestamp' == $format) {
            return [
                'begin' => $begin,
                'end'   => $end,
            ];
        }

        return [
            'begin' => date($format, $begin),
            'end'   => date($format, $end),
        ];
    }

    /**
     * @param        $year
     * @param        $month
     * @param string $format
     *
     * @return array
     *
     * @deprecated use Datetime instead, pls
     */
    public static function getMonthBeginEndDay($year, $month, $format = 'timestamp')
    {
        $datetime          = new Datetime();
        list($begin, $end) = $datetime->monthBeginEnd($year, $month);
        if ('timestamp' == $format) {
            return [
                'begin' => $begin,
                'end'   => $end,
            ];
        }

        return [
            'begin' => date($format, $begin),
            'end'   => date($format, $end),
        ];
    }

    /**
     * 数组排序.
     *
     * @param array        $array
     * @param array|string $sortRule example : ['<filed-name>'=>SORT_ASC,'<filed-name>'=>SORT_DESC] or filed-name
     * @param string       $order
     *
     * @return mixed
     *
     * @deprecated
     */
    public static function arraySort($array, $sortRule = [], $order = 'asc')
    {
        $Arr = new ArrayMap($array);
        if (\is_string($sortRule)) {
            $sortRule = [$sortRule => $order];
        }

        return $Arr->sort(null, $sortRule);
    }

    /**
     * 获取客户端IP地址
     *
     * @param int  $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv  是否进行高级模式获取（有可能被伪装）
     *
     * @return mixed
     */
    public static function getClientIp($type = 0, $adv = false)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;
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
        // IP地址合法验证
        $long = sprintf('%u', ip2long($ip));
        $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];

        return $ip[$type];
    }

    public static function uuid($salt = '')
    {
        return md5($salt . uniqid(md5(microtime(true)), true));
    }

    public static function uuidAddFlavour($salt = '', $cut = 8, $flavour = '-', $isUpper = false)
    {
        $str    = self::uuid($salt);
        $len    = \strlen($str);
        $length = $len;
        $uuid   = '';
        if (\is_array($cut)) {
            while ($length > 0) {
                $uuid .= substr($str, $len - $length, array_rand($cut)) . $flavour;
                $length -= $cut;
            }
        } elseif (\is_int($cut)) {
            $step = 0;
            while ($length > 0) {
                $temp   = substr($str, $len - $length, $cut);
                $uuid .= 0 != $step ? $flavour . $temp : $temp;
                $length -= $cut;
                ++$step;
            }
        }

        return $isUpper ? strtoupper($uuid) : $uuid;
    }

    public static function checkDataToString(&$array = [])
    {
        if (\is_array($array)) {
            foreach ($array as &$a) {
                if (\is_array($a)) {
                    $a = self::checkDataToString($a);
                }
                if (\is_int($a)) {
                    $a = (string) $a;
                }
                if (null === $a) {
                    $a = '';
                }
            }
        } elseif (\is_int($array)) {
            $array = (string) $array;
        } elseif (null === $array) {
            $array = '';
        }

        return $array;
    }
}
