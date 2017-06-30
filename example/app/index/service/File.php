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

namespace example\index\service;

class File{
    public static function save($filename,$content='default',$append=true){
        if(is_array($content) || is_object($content)){
            $content = dump($content,false);
        }
        $file_content = file_exists($filename)? file_get_contents($filename):'';

        if($append){
            $content = $file_content.$content."\r\n\r\n";
        }

        return file_put_contents($filename,$content);
    }
}