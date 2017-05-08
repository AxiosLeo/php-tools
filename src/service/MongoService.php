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

use MongoDB\Driver\Manager;
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

    public function getCheckConnect(){
        $config = self::$config;

        $mongo = new Manager('mongodb://'. ($config['username'] ? "{$config['username']}" : '') . ($config['password'] ? ":{$config['password']}@" : '') . $config['hostname'] . ($config['hostport'] ? ":{$config['hostport']}" : '') . '/' . ($config['database'] ? "{$config['database']}" : ''));
        $mongo->getServers();
    }
}