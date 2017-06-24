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
/**
 * Class EnvService
 * @package axios\tpr\service
 */
class EnvService {
    public static $env_path;
    public static $env_array_section ;
    public static $env_array ;
    public static $instance;
    public static $needBack;

    private function __construct($env_path=''){
        if(file_exists($env_path)){
            self::$env_path = $env_path;
        }else{
            defined('ROOT_PATH') or define('ROOT_PATH',__DIR__.'/../../../../../');
            self::$env_path = ROOT_PATH.'.env';
        }
        self::$env_array_section = parse_ini_file(self::$env_path, true);
        self::$env_array = parse_ini_file(self::$env_path, false);
    }

    private static function init(){
        if(self::$instance===null){
            self::$instance = new static();
        }
    }

    public static function select($env_path=''){
        if(is_null(self::$instance) || !empty($env_path)){
            self::$instance = new static($env_path);
        }
        return  self::$instance ;
    }

    public static function all($section=true){
        self::init();
        return $section ? self::$env_array_section:self::$env_array;
    }

    public static function get($index,$default=''){
        self::init();
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
        self::init();
        $envArraySection = self::$env_array_section;
        $envArray = self::$env_array;
        if(strpos($index,'.')){
            $indexArray = explode('.',$index);
            $tmpSection = &$envArraySection;
            $tmp = &$envArray;
            $indexLen = count($indexArray);
            foreach ($indexArray as $key=>$i){
                if(!isset($tmpSection[$i])){
                    return false;
                }
                //final
                if($key==$indexLen-1){
                    $tmpSection[$i] = $value;
                    $tmp[$i] = $value;
                }else{
                    if($key!=0){
                        $tmp = &$tmp[$i];
                    }
                    $tmpSection = &$tmpSection[$i];
                }
            }
        }
        self::$env_array_section = $envArraySection;
        self::$env_array = $envArray;
        return self::get($index);
    }

    public static function save(){
        $envSection = self::$env_array_section;
        $text = self::envFileString($envSection);
        return file_put_contents(self::$env_path,$text);
    }

    private static function envFileString($data){
        $str = "\r\n";

        foreach ($data as $k1=>$v1){
            $str .= "[".$k1."]\r\n";
            foreach ($v1 as $k2=>$v2){
                if(is_array($v2)){
                    foreach ($v2 as $k3=>$v3){
                        $str .= $k2.'['.$k3.'] = '.$v3."\r\n";
                    }
                }else{
                    $str .= $k2.' = '.$v2."\r\n";
                }
            }
            $str .="\r\n";
        }
        return $str;
    }

}