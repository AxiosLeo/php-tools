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

use think\Request;

class RequestEnd{
    public $param;
    public $request;
    function __construct()
    {
        $this->request = Request::instance();
        $this->param = $this->request->param();
    }

    public function run(){
        posix_kill(posix_getpid(), SIGINT);
        exit();
    }
}