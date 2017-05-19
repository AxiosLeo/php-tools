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
use axios\tpr\service\LangService;
use axios\tpr\core\Result;
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
        $this->middleware();
        $this->cache();
        $this->filter();
    }

    private function filter(){
        $class = Loader::parseClass($this->module, 'validate',$this->controller,false);

        if(class_exists($class)){
            $Validate = Loader::validate($this->controller, 'validate', false,$this->module);
            $check = isset($filter['scene'])?$Validate->scene($this->action)->check($this->param):$Validate->check($this->param);
            if(!$check){
                Result::wrong(400,LangService::trans($Validate->getError()));
            }
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