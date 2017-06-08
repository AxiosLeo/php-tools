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

namespace axios\tpr\behavior;

use think\Env;
use think\Lang;
use think\Request;
use axios\tpr\core\Result;

class AppInit {
    public $param;
    public $request;
    function __construct()
    {
        $this->request = Request::instance();
        $this->param = $this->request->param();
    }

    public function run(){
        $this->lang();
        $this->sign();
    }

    public function lang(){
        $lang_path = defined('LANG_PATH')?LANG_PATH:CONF_PATH.'lang'.DIRECTORY_SEPARATOR;
        $lastString = substr($lang_path,-1);
        if($lastString!=DS){
            $lang_path .= DS;
        }
        Lang::load($lang_path. $this->request->langset() . EXT);
    }

    public function sign(){
        $status = Env::get('sign.status');

        if($status){
            $timestamp = Env::get('sign.timestamp_name');
            if(empty($timestamp)){
                $timestamp = 'timestamp';
            }
            if(!isset($this->param[$timestamp])){
                Result::wrong(401,$timestamp.' param not exits');
            }
            $timestamp = $this->param[$timestamp];

            $expire = Env::get('sign.expire');
            if(empty($expire)){
                $expire = 10;
            }
            if(time()-intval($timestamp) > intval($expire)){
                Result::wrong(401,md5($timestamp."azXCz5AEabA1Y9XhB").'sign timeout'.time());
            }

            $sign_name = Env::get('sign.sign_mame');
            if(empty($sign_name)){
                $sign_name = 'sign';
            }
            if(!isset($this->param[$sign_name])){
                Result::wrong(401,$sign_name.' param not exits');
            }

            $sign = $this->param[$sign_name];
            $sign_result = check_sign($timestamp,$sign);

            if($sign_result!==true){
                Result::wrong(401,'wrong sign');
            }
        }
    }
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        unset($this->request);
        unset($this->param);
    }


}