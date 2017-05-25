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

use think\Config;

class LangService {

    public static function trans($message){
        $messageArray = explode('@', $message);
        $str = '';
        foreach ($messageArray as $k=>$m){
            if(empty($m)){
                continue;
            }
            $tmp = lang($m);
            if($m===$tmp){
                $str.=$k==0?$m:" ".$m;
            }else{
                $str.=$tmp;
            }
        }
        return $str;
    }

    public static function message($code){
        if(Config::has('code.'.$code)){
            $message = Config::get("code.".$code);
            return LangService::trans($message);
        }else{
            return "";
        }
    }
}