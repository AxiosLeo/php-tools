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

class Test {
    public function files(){
        $filename = ROOT_PATH.'test';
        if(file_exists($filename)){
            $fp = fopen($filename,'w');
            if(flock($fp,LOCK_EX)){
                $content = file_get_contents($filename);
                fwrite($fp,$content.posix_getpid()."\r\n");
                sleep(rand(1,5));
                flock($fp , LOCK_UN);
            }
            fclose($fp);
        }
    }
}