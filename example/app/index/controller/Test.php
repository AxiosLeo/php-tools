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

namespace tpr\index\controller;

use axios\tpr\core\Api;
use axios\tpr\service\EnvService;

class Test extends Api{
    public function index(){
        echo 'index';
    }
    public function env(){
        $env_dir = ROOT_PATH.".env2";
        dump($env_dir);
//        EnvService::select($env_dir);
//        $env = EnvService::all();
//        dump($env);
//        $env = EnvService::all(false);
//        dump($env);
//        EnvService::select($env_dir);
//        EnvService::select($env_dir);
        EnvService::select($env_dir);
        dump(EnvService::all());
//        dump(EnvService::all(false));
        dump(EnvService::get('test.test.0'));
        dump(EnvService::set('test.test.0',time()));
        dump(EnvService::get('test.test.0'));
        dump(EnvService::all());
        dump(EnvService::save());
//        dump(EnvService::all(false));
    }
}