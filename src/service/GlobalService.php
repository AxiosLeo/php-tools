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

namespace axios\tpr\service;

use axios\tpr\controller\ApiBase;

class GlobalService extends ApiBase{

    public static function api(){
        return new self();
    }

    public static function set($name,$value){
        define(self::name($name),$value);
    }

    public static function get($name=''){
        if(!defined(self::name($name))){
            return false;
        }
        $defined = get_defined_constants(true);
        if(isset($defined['user'][self::name($name)])){
            return $defined['user'][self::name($name)];
        }
        return "";
    }

    private static function name($name=''){
        return "TPR_".strtoupper($name);
    }

    public function __invoke($name='')
    {
        // TODO: Implement __invoke() method.
        return self::get(self::name($name));
    }
}