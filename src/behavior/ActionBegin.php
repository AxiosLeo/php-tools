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

use axios\tpr\core\Cache;
use axios\tpr\service\EnvService;
use axios\tpr\service\LangService;
use axios\tpr\core\Result;
use think\Validate;
use think\Request;
use think\Loader;

class ActionBegin{
    public $param;
    public $request;
    public $module;
    public $controller;
    public $action;
    function __construct()
    {
        $this->request    = Request::instance();
        $this->param      = $this->request->param();
        $this->module     = strtolower($this->request->module());
        $this->controller = strtolower($this->request->controller());
        $this->action     = $this->request->action();
    }

    public function run(){
        $this->sign();
        $class = Loader::parseClass($this->module, 'validate',$this->controller,false);
        if(class_exists($class)){
            $Validate = Loader::validate($this->controller, 'validate', false,$this->module);
            $this->filter($Validate);
        }

        $this->middleware();
        $this->cache();
    }

    public function sign(){
        $status = EnvService::get('sign.status',0);

        if($status){
            $timestamp = EnvService::get('sign.timestamp_name','timestamp');
            if(!isset($this->param[$timestamp])){
                Result::wrong(401,$timestamp.' param not exits');
            }
            $timestamp = $this->param[$timestamp];

            $expire = EnvService::get('sign.expire',10);
            if(time()-intval($timestamp) > intval($expire)){
                Result::wrong(401,md5($timestamp."azXCz5AEabA1Y9XhB").'sign timeout'.time());
            }

            $sign_name = EnvService::get('sign.sign_mame','sign');
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

    /**
     * @param Validate $Validate
     */
    private function filter($Validate){
        $check = $Validate->hasScene($this->action) ? $Validate->scene($this->action)->check($this->param):true;
        if(!$check){
            Result::wrong(400,LangService::trans($Validate->getError()));
        }
    }

    private function cache(){
        $cache = Cache::get($this->request);
        if(!empty($cache)){
            Result::send($cache);
        }
    }

    private function middleware(){
        $class = Loader::parseClass(strtolower($this->module), 'middleware',strtolower($this->controller),false);
        if(class_exists($class)){
            $Middleware = Loader::validate($this->controller, 'middleware', false,$this->module);
            call_user_func_array([$Middleware,'before'],array($this->request));
        }
    }
}