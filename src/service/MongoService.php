<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/4/10 15:59
 */
namespace axios\tpr\service;

use think\Config;
use think\Db;

/**
 * Class MongoService
 * @package axios\tpr\service
 */
class MongoService{
    public static $config = [];
    public static function connect($select='default'){
        self::$config = Config::get('mongo.'.$select);
        return new self();
    }
    public static function name($name=''){
        if(empty(self::$config)){
            self::$config =  Config::get('mongo.default');
        }
        return Db::connect(self::$config )->name($name);
    }
    public function __call($name, $arguments)
    {
        return Db::connect(self::$config )->name($arguments);
    }
}