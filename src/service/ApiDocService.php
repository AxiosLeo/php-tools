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

use think\Route;

class ApiDocService{
    public static $instance;
    public static $dir;
    public static $apiClassList;
    public static $connector = ';';
    private static $isConnect = false;
    private static $content = '';

    public static $typeList = [
        'char', 'string', 'int', 'float', 'boolean',
        'date', 'array', 'fixed', 'enum', 'object',
    ];

    function __construct($dir = APP_PATH)
    {
        self::$dir = $dir;
        self::$apiClassList = self::scanApiClass($dir);
    }

    public static function config($dir=APP_PATH,$connector=';'){
        self::$connector = $connector;
        if(is_null(self::$instance)){
            return new static($dir);
        }
        self::$dir = $dir;
        return self::$instance;
    }

    public static function doc($class=''){
        if(empty(self::$dir)){
            self::config();
        }
        $list = [];$n=0;
        if(!empty($class)){
            $list = self::makeClassDoc($class);
        }else{
            foreach (self::$apiClassList as $k=>$apiClassDir){
                $doc =  self::makeClassDoc($apiClassDir);
                if(!empty($doc)){
                    $list[$n++] = $doc;
                }
            }
        }

        return $list;
    }

    public static function makeClassDoc($class=''){
        if(empty(self::$dir)){
            self::config();
        }
        $doc = [];
        if(class_exists($class)){
            $reflectionClass = new \ReflectionClass($class);
            $doc['name'] = $reflectionClass->name;
            $doc['file_name'] = $reflectionClass->getFileName();
            $doc['short_name'] = $reflectionClass->getShortName();
            $comment = self::trans($reflectionClass->getDocComment());
            $doc['comment'] = $comment;

            $_getMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
            $methods = [];$m=0;
            foreach ($_getMethods as $key=>$method){
                if($method->class==$class){
                    $methods[$m] = self::makeMethodDoc($class,$method->name);
                    $m++;
                }
            }
            $doc['methods'] = $methods;
        }
        return $doc;
    }

    public static function makeMethodDoc($class,$method_name){
        if(empty(self::$dir)){
            self::config();
        }
        $reflectionClass = new \ReflectionClass($class);
        $method = $reflectionClass->getMethod($method_name);
        $temp = explode("\\",$class);
        $m = [];
        $m['name'] = $method->name;
        $m['path'] = strtolower($temp[1])."/".strtolower($temp[3])."/".$method->name;
        $rule =  Route::name($m['path']);
        $route = '';
        if(!empty($rule)){
            $route = $rule[0][0];
        }
        $m['route'] = $route;
        $method_comment = self::trans($method->getDocComment());
        $m['comment'] = $method_comment;
        return $m;
    }

    private static function trans($comment){
        $docComment = $comment;
        $data = [];
        if ($docComment !== false) {
            $docCommentArr = explode("\n", $docComment);
            foreach ($docCommentArr as $comment){
                //find @ position
                $posA = strpos($comment,'@');
                if($posA===false){
                    continue;
                }
                $content = trim(substr($comment, $posA));
                $needle_length = strpos($content,' ');
                if($needle_length === false){
                    $needle = str_replace('@','',trim($content));
                    $content = '';
                }else{
                    $needle = trim(substr($content,1,$needle_length));
                    $content = trim(substr($content, $needle_length));
                    $content = self::transContent($content);
                }
                if($content===true){
                    continue;
                }
                if(isset($data[$needle])){
                    if(is_array($data[$needle])){
                        array_push($data[$needle],$content);
                    }else{
                        $tmp = $data[$needle];
                        $data[$needle] = [];
                        $data[$needle][0] = $tmp;
                        $data[$needle][1] = $content;
                    }
                }else{
                    if(is_array($content)){
                        $data[$needle][0]=$content;
                    }else{
                        $data[$needle] = $content;
                    }
                }
            }
        }

        return $data;
    }

    private static function transContent($content){
        $connector = self::$connector;
        self::$isConnect = strpos($content,$connector)===false?false:true;
        self::$content = self::$content.$content;
        if(self::$isConnect){
            return true;
        }
        $content = self::$content;
        self::$content = '';
        if(strpos($content,' ')!==false){
            $contentArray = explode(' ',$content);
            if(isset($contentArray[0]) && !in_array($contentArray[0],self::$typeList)){
                return $content;
            }
            $data = [
                'type'=>isset($contentArray[0])?$contentArray[0]:'',
                'name'=>isset($contentArray[1])?$contentArray[1]:'',
                'desc'=>isset($contentArray[2])?$contentArray[2]:''
            ];
            $content = $data;
        }
        return $content;
    }

    private static function deepScanDir($dir) {
        $fileArr = array ();
        $dirArr = array ();
        $dir = rtrim($dir, '//');
        if (is_dir($dir)) {
            $dirHandle = opendir($dir);
            while (false !== ($fileName = readdir($dirHandle))) {
                $subFile = $dir . DIRECTORY_SEPARATOR . $fileName;
                if (is_file($subFile)) {
                    $fileArr[] = $subFile;
                }
                elseif (is_dir($subFile) && str_replace('.', '', $fileName) != '') {
                    $dirArr[] = $subFile;
                    $arr = self::deepScanDir($subFile);
                    $dirArr = array_merge($dirArr, $arr['dir']);
                    $fileArr = array_merge($fileArr, $arr['file']);
                }
            }
            closedir($dirHandle);
        }
        return array (
            'dir' => $dirArr,
            'file' => $fileArr
        );
    }

    private static function scanApiClass($dir=APP_PATH){
        $scan = self::deepScanDir($dir);
        $files = $scan['file'];
        $n=0;$ApiList = [];
        foreach ($files as $k=>$f){
            if(strpos($f,"controller")!==false){
                require_once $f;
                if(strpos($f,'common')===false){
                    $content = file_get_contents($f);
                    $namespace_begin = strpos($content,'namespace')+10;
                    $namespace_end = strpos($content,';');
                    $namespace = substr($content,$namespace_begin,$namespace_end-$namespace_begin);
                    $class_name = basename($f,'.php');
                    $class = $namespace.'\\'.$class_name;
                    $ApiList[$n++]=$class;
                }
            }
        }
        return $ApiList;
    }
}