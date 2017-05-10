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

namespace axios\tpr\exception;

use Exception;
use think\Env;
use think\exception\Handle;
use think\Response;

class Http extends Handle{
    public function render(Exception $e)
    {
        //TODO::开发者对异常的操作
        //可以在此交由系统处理
        if(Env::get('debug.status')){
            return parent::render($e);
        }else{
            $req['code']= "500";
            $req['message'] = "something error";
            $req['data'] = [];
            $return_type = Env::get('response.return_type');
            if(empty($return_type)){
                $return_type = "json";
            }
            Response::create($req,$return_type,"500")->send();
            die();
        }
    }
}