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
    public static function api($class=''){
        $ApiClass = self::scanApiClass();
        $list = [];$n=0;
        if(!empty($class)){
            $list = self::makeClassDoc($class);
        }else{
            foreach ($ApiClass as $k=>$api){
                $doc =  self::makeClassDoc($api);
                if(!empty($doc)){
                    $list[$n++] = $doc;
                }
            }
        }

        return $list;
    }

    public static function makeClassDoc($class=''){
        $doc = [];
        if(class_exists($class)){
            $reflectionClass = new \ReflectionClass($class);
            $doc['name'] = $reflectionClass->name;
            $doc['file_name'] = $reflectionClass->getFileName();
            $doc['short_name'] = $reflectionClass->getShortName();
            $comment = self::trans($reflectionClass->getDocComment());
            $doc['view']=$comment['view'];
            $doc['title'] = $comment['title'];
            $doc['desc'] = $comment['desc'];
            $doc['package']=$comment['package'];
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
        $reflectionClass = new \ReflectionClass($class);
        $method = $reflectionClass->getMethod($method_name);
        $temp = explode("\\",$class);
        $m = [];
        $m['name'] = $method->name;
        if(in_array('v1',$temp)){
            $m['path'] = strtolower($temp[1])."/".strtolower($temp[3]).'.'.strtolower($temp[4])."/".$method->name;
        }else{
            $m['path'] = strtolower($temp[1])."/".strtolower($temp[3])."/".$method->name;
        }

        $rule =  Route::name($m['path']);
        $route = '';
        if(!empty($rule)){
            $route = $rule[0][0];
        }
        $m['route'] = $route;
        $method_comment = self::trans($method->getDocComment());
        $m['view'] = $method_comment['view']=="@view"?"":$method_comment['view'];
        $m['title'] = $method_comment['title']=="@title"?$method->name:$method_comment['title'];
        $m['desc'] = $method_comment['desc']=="@desc"?"":$method_comment['desc'];
        $m['method'] = $method_comment['method']=="@method"?"":strtoupper($method_comment['method']);
        $m['parameter'] = $method_comment['parameter'];
        $m['header'] = $method_comment['header'];
        $m['response'] = $method_comment['response'];
        return $m;
    }

    public static function trans($comment){
        $view  = '@view';
        $title  = '@title';
        $desc   = '@desc';
        $method = '';
        $package= '@package';
        $param  = [];
        $param_count  = 0;
        $response = [];
        $response_count = 0;
        $header = [];
        $header_count = 0;

        $docComment = $comment;
        if ($docComment !== false) {
            $docCommentArr = explode("\n", $docComment);
            $comment = trim($docCommentArr[1]);
            $title = trim(substr($comment, strpos($comment, '*') + 1));

            foreach ($docCommentArr as $comment) {
                //@view
                $pos = stripos($comment, '@view');
                if ($pos !== false) {
                    $view = trim(substr($comment, $pos + 5));
                }
                //@desc
                $pos = stripos($comment, '@desc');
                if ($pos !== false) {
                    $desc = trim(substr($comment, $pos + 5));
                }

                //@package
                $pos = stripos($comment, '@package');
                if ($pos !== false) {
                    $package = trim(substr($comment, $pos + 8));
                }

                //@method
                $pos = stripos($comment, '@method');
                if ($pos !== false) {
                    $method = trim(substr($comment, $pos + 8));
                }

                //@response
                $pos = stripos($comment, '@response');
                if($pos !== false){
                    $temp = explode(" ",trim(substr($comment,$pos + 9)));
                    $tn = 0;$tt=[];
                    foreach ($temp as $k=>$t){
                        if(empty($t)){
                            unset($temp[$k]);
                        }else{
                            $tt[$tn++]=$t;
                        }
                    }
                    $temp = $tt;
                    $response[$response_count]['type'] = isset($temp[0]) ?LangService::trans($temp[0]):"";
                    $response[$response_count]['name'] = isset($temp[1]) ?$temp[1]:"";
                    $response[$response_count]['info'] = isset($temp[2]) ?$temp[2]:"";
                    $response_count++;
                }

                //@parameter
                $pos = stripos($comment, '@parameter');
                if($pos !== false){
                    $temp = explode(" ",trim(substr($comment,$pos + 10)));
                    $tn = 0;$tt=[];
                    foreach ($temp as $k=>$t){
                        if(empty($t)){
                            unset($temp[$k]);
                        }else{
                            $tt[$tn++]=$t;
                        }
                    }
                    $temp = $tt;
                    $param[$param_count]['type'] = isset($temp[0]) ?LangService::trans($temp[0]):"";
                    $param[$param_count]['name'] = isset($temp[1]) ?$temp[1]:"";
                    $param[$param_count]['info'] = isset($temp[2]) ?$temp[2]:"";
                    $param_count++;
                }

                //@header
                $pos = stripos($comment, '@header');
                if($pos !== false){
                    $temp = explode(" ",trim(substr($comment,$pos + 7)));
                    $tn = 0;$tt=[];
                    foreach ($temp as $k=>$t){
                        if(empty($t)){
                            unset($temp[$k]);
                        }else{
                            $tt[$tn++]=$t;
                        }
                    }
                    $temp = $tt;
                    $header[$header_count]['type'] = isset($temp[0]) ?LangService::trans($temp[0]):"";
                    $header[$header_count]['name'] = isset($temp[1]) ?$temp[1]:"";
                    $header[$header_count]['info'] = isset($temp[2]) ?$temp[2]:"";
                    $header_count++;
                }

            }
        }

        $comment = [
            'view' => $view,
            'title' => $title,
            'desc'  => $desc,
            'package'=>$package,
            'header' => $header,
            'parameter' => $param,
            'method'=>$method,
            'response'=>$response
        ];

        return $comment;
    }

    public static function deepScanDir($dir) {
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

    public static function scanApiClass(){
        $scan = self::deepScanDir(APP_PATH);
        $files = $scan['file'];
        foreach ($files as $k=>$f){
            if(strpos($f,"app")!==false && strpos($f,"controller")!==false && strpos($f,"common")===false){
                require_once $f;
            }
        }
        $class = get_declared_classes();
        $n=0;$ApiList = [];
        foreach ($class as $k=>$c){
            if(strpos($c,"app")!==false && strpos($c,"controller")!==false && strpos($c,"common")===false && strpos($c,'admin')===false){
                $ApiList[$n++]=$c;
            }
        }
        return $ApiList;
    }
}