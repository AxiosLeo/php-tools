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

namespace axios\tpr\core;

use think\Controller;
use think\Request;

class Api extends Controller{
    protected $param;

    protected $return_type;

    protected $toString = true;

    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->param = $this->request->param();
    }

    protected function wrong($code,$message=''){
        $this->rep([],$code,$message);
    }

    protected function rep($data=[],$code=200,$message='',array $header=[]){
        $req = Result::instance($this->return_type,$this->toString)->rep($data,$code,$message,$header);
        $this->request->req = $req;
        Cache::set($req,$this->request);
    }

    protected function response($data=[],$code=200,$message='',array $header=[]){
        $this->rep($data,$code,$message,$header);
    }

    public function __empty(){
        $this->wrong(404);
    }
}