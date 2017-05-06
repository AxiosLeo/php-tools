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
        $error = explode('@', $message);
        $str = '';
        foreach ($error as $e){
            $tmp = lang($e);
            if($e===$tmp){
                $str.=lang($e)." ";
            }else{
                $str.=lang($e);
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