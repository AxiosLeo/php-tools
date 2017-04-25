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
        define($name,$value);
    }

    public static function get($name=''){
        if(!defined($name)){
            return false;
        }
        $defined = get_defined_constants(true);
        if(isset($defined['user'][$name])){
            return $defined['user'][$name];
        }
        return "";
    }

    public function __invoke($name='')
    {
        // TODO: Implement __invoke() method.
        return self::get($name);
    }
}