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

class AppEnd{

    public $request;

    public $req;

    function __construct()
    {
        $this->request    = Request::instance();
        $this->req        = $this->request->req;
    }

    public function run(){
        $this->cache();
    }

    private function cache(){
        Cache::set($this->req,$this->request);
    }
}