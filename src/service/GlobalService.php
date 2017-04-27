<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/4/16 21:23
 */
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