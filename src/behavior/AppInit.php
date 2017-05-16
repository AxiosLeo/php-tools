<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/16 11:26
 */
namespace axios\tpr\behavior;

use think\Env;
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
        $this->sign();
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