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

use MongoDB\Driver\Exception\ConnectionException;
use think\Config;
use think\Db;

/**
 * Class MongoService
 * @package axios\tpr\service
 */
class MongoService{
    public static $config = [
        "type"              => '\think\mongo\Connection',
        "hostname"          => '127.0.0.1',
        "database"          => 'test',
        "username"          => 'test',
        "password"          => '123456',
        "hostport"          => '27017',
        "dsn"               => '',
        "params"            => [],
        "charset"           => "utf8",
        "pk"                => "_id",
        "pk_type"           => "ObjectID",
        "prefix"            => "",
        "debug"             => false,
        "deploy"            => 0,
        "rw_separate"       => false,
        "master_num"        => 1,
        "slave_no"          => "",
        "fields_strict"     => true,
        "resultset_type"    => "array",
        "auto_timestamp"    => false,
        "datetime_format"   => "Y-m-d H:i:s",
        "sql_explain"       => false,
        "pk_convert_id"     => false,
        "type_map" => [
            "root" =>"array",
            "document"=>"array",
            "query" =>"\\think\\mongo\\Query"
        ]
    ];
    public static $errorMsg = '';
    public static $errorCode = 0;
    public static $instance;
    public static $MongoDb;
    public function __construct($config = [])
    {
        if(is_string($config)){
            $config = Config::get('mongo.'.$config);
        }
        $config = !empty($config) ? $config : Config::get('mongo.default');
        self::$config = array_merge(self::$config,$config);
        self::$MongoDb = Db::connect(self::$config);
    }

    public static function connect($config=''){
        self::$instance = new static($config);
        return self::$instance;
    }

    private static function init(){
        if(self::$instance===null){
            self::connect();
        }
    }

    public static function name($name=''){
        self::init();
        return self::$MongoDb->name($name);
    }

    public static function checkConnect($config=[]){
        self::connect($config);
        try{
            self::connect(self::$config)->name('test')->select();
            return true;
        }
        catch (ConnectionException $e){
            self::$errorMsg = $e->getMessage();
            self::$errorCode = $e->getCode();
            return false;
        }
    }
}