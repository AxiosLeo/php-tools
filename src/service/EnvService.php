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

class EnvService {
    public static $env_path = '';
    public static $env_array_section = [];
    public static $env_array = [];
    public static $instance;

    public function __construct($env_path='')
    {
        if(file_exists($env_path)){
            self::$env_path = $env_path;
        }else{
            self::$env_path =  ROOT_PATH.".env";
        }
        self::$env_array_section = parse_ini_file(self::$env_path, true);
        self::$env_array_section = parse_ini_file(self::$env_path, false);
    }

    public static function select($env_path){
        if(is_null(self::$instance)){
            self::$instance = new static($env_path);
        }
        return  self::$instance ;
    }

    public static function all(){
        return self::$env_array_section;
    }

    public static function get($index,$default=''){
        if(strpos($index,'.')){
            $indexArray = explode('.',$index);
            $envData = self::$env_array_section;
            $tmp = $envData;
            foreach ($indexArray as $i){
                $tmp = isset($tmp[$i])?$tmp[$i]:null;
                if(is_null($tmp)){
                    return $default;
                }
            }
            return $tmp;
        }

        $tmp = self::$env_array;
        $tmp = isset($tmp[$index])?$tmp[$index]:null;
        if(is_null($tmp)){
            return $default;
        }
        return $tmp;
    }

    public static function set($index,$value){
        if(strpos($index,'.')){
            $indexArray = explode('.',$index);
            $envData = self::$env_array_section;
            $tmp = &$envData;
            foreach ($indexArray as $i){
                if(!isset($tmp[$i])){
                    return false;
                }
                $tmp = &$tmp[$i];
            }
            $tmp = $value;
        }
        return false;
    }


}