<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/16 16:36
 */
namespace axios\tpr\behavior;

use axios\tpr\core\Cache;
use think\Request;
use think\Loader;

class AppEnd{
    public $param;
    public $request;
    public $module;
    public $controller;
    public $action;
    public $req;
    function __construct()
    {
        $this->request    = Request::instance();
        $this->param      = $this->request->param();
        $this->module     = strtolower($this->request->module());
        $this->controller = strtolower($this->request->controller());
        $this->action     = $this->request->action();
        $this->req        = $this->request->req;
    }

    public function run(){
        $this->middleware();
        $this->cache();
    }

    private function middleware(){
        $class = Loader::parseClass(strtolower($this->module), 'middleware',strtolower($this->controller),false);
        if(class_exists($class)){
            $Middleware = Loader::validate($this->controller, 'validate', false,$this->module);
            call_user_func_array([$Middleware,'after'],array($this->request));
        }
    }

    private function cache(){
        Cache::set($this->req,$this->request);
    }
}