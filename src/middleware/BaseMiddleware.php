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

namespace axios\tpr\middleware;

use axios\tpr\service\GlobalService;

class BaseMiddleware {
    protected $param ;

    protected $method ;

    protected $identify ;

    protected $response = [];

    function __construct()
    {
        $this->param = GlobalService::get('param');
        $this->method= GlobalService::get('method');
        $this->identify = GlobalService::get('identify');
        $this->response = GlobalService::get('req');
    }

    protected function get($name='param'){
        return GlobalService::get($name);
    }
}