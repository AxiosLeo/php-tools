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
}