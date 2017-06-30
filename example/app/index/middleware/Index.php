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

namespace example\index\middleware;

use axios\tpr\core\Middleware;
use think\Log;
use think\Request;

class Index extends Middleware {
    public function before(Request $request = null)
    {
        // TODO: Implement before() method.
//        Log::record($request,'debug');
    }

    public function after(Request $request = null, array $response = [])
    {
        // TODO: Implement after() method.
        sleep(3);
//        Log::record($request,'debug');
//        Log::record($response,'debug');
    }
}