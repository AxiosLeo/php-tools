<?php
// +----------------------------------------------------------------------
// | TPR [ Design For Api Develop ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2017 http://hanxv.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axios <axioscros@aliyun.com>
// +----------------------------------------------------------------------

namespace axios\tpr\service;

class ToolService {
    public static function uuid($salt=''){
        return md5($salt.uniqid(md5(microtime(true)),true));
    }

    public static function uuidAddFlavour($salt='',$cut=8,$flavour='-',$isUpper=false){
        $str = self::uuid($salt);
        $len = strlen($str);$length = $len;$uuid='';
        if(is_array($cut)){
            while ($length>0){
                $uuid .= substr($str,$len-$length,array_rand($cut)).$flavour;
                $length -=$cut;
            }
        }else if(is_int($cut)){
            $step = 0;
            while ($length>0){
                $temp = substr($str,$len-$length,$cut);
                $uuid .= $step!=0 ? $flavour.$temp:$temp;
                $length -=$cut;
                $step++;
            }
        }
        return $isUpper?strtoupper($uuid):$uuid;
    }

    public static function token($salt=''){
        $str = md5($salt.uniqid(md5(microtime(true)),true));
        return $str;
    }

    public static function identity($identity=0){
        $shm = ftok(__FILE__, 'h');
        $shm_id = shmop_open($shm,'c',0644,1);
        if($identity===0){
            return shmop_read($shm_id,0,1);
        }
        shmop_write($shm_id,$identity,0);
        return $identity;
    }

    public static function getMonthBeginEndDay($year,$month,$format='timestamp'){
        $month = sprintf('%02d',$month);
        $ymd = $year."-".$month."-01";
        $begin = strtotime($ymd." 00:00:00");
        $end   = strtotime("$ymd +1 month -1 seconds");
        if($format=='timestamp'){
            return [
                'begin'=>$begin,
                'end'=>$end
            ];
        }else{
            return [
                'begin'=>date($format,$begin),
                'end'=>date($format,$end),
            ];
        }
    }

    public static function getDayBeginEndTime($date,$format='timestamp'){
        $begin = strtotime($date." 00:00:00");
        $end   = strtotime("$date +1 day -1 seconds");
        if($format=='timestamp'){
            return [
                'begin'=>$begin,
                'end'=>$end
            ];
        }else{
            return [
                'begin'=>date($format,$begin),
                'end'=>date($format,$end),
            ];
        }
    }

    public static function getHourBeginEndTime($date ,$hour,$format='timestamp'){
        $hour = sprintf('%02d',$hour);
        $begin = strtotime($date." ".$hour.":00:00");
        $end   = strtotime($date." ".$hour.":00:00 +1 hour -1 seconds");
        if($format=='timestamp'){
            return [
                'begin'=>$begin,
                'end'=>$end
            ];
        }else{
            return [
                'begin'=>date($format,$begin),
                'end'=>date($format,$end),
            ];
        }
    }
}