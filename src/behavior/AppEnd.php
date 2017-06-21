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
use think\Request;
use think\Loader;
use think\Config;

class AppEnd{
    public $param;
    public $request;
    public $module;
    public $controller;
    public $action;
    public $req;
    public $mca;
    function __construct()
    {
        $this->request    = Request::instance();
        $this->param      = $this->request->param();
        $this->module     = strtolower($this->request->module());
        $this->controller = strtolower($this->request->controller());
        $this->action     = $this->request->action();
        $this->req        = $this->request->req;
        $this->mca        = $this->request->mca;
    }

    public function run(){
        $this->middleware();
        $this->cache();
    }

    private function middleware(){
        $middleware_config =  Config::get('middleware.after');
        if(isset($middleware_config[$this->mca])){
            $middleware_config = $middleware_config[$this->mca];
            $Middleware = validate($middleware_config[0]);
            call_user_func_array([$Middleware,$middleware_config[1]],[$this->request]);
        }else{
            $class = Loader::parseClass(strtolower($this->module), 'middleware',strtolower($this->controller),false);
            if(class_exists($class)){
                $Middleware = Loader::validate($this->controller, 'validate', false,$this->module);
                call_user_func_array([$Middleware,'after'],array($this->request,$this->req));
            }
        }
    }

    private function cache(){
        Cache::set($this->req,$this->request);
    }
}