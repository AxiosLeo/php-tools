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

namespace example\index\controller;

use axios\tpr\core\Api;
use axios\tpr\service\EnvService;
use axios\tpr\service\MongoService;

class Test extends Api{
    public function index(){
        echo 'index';
    }
    public function env(){
        $env_dir = ROOT_PATH.".env2";
        dump($env_dir);
        EnvService::select($env_dir);
        dump(EnvService::all());
        dump(EnvService::get('test.test.0'));
        dump(EnvService::set('test.test.0',time()));
        dump(EnvService::get('test.test.0'));
        dump(EnvService::all());
        dump(EnvService::save());
    }
    public function mongo(){
        $result = MongoService::checkConnect();
        dump($result);
        dump(MongoService::$errorMsg);
    }
}