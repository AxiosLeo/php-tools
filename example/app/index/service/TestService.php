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

namespace tpr\index\service;

use think\Log;

class TestService {
    public function test($a,$b){
        $sleep = $b;
        sleep($sleep);
//        $test = new Test();
//        $content = file_exists(ROOT_PATH.'test.txt')?file_get_contents(ROOT_PATH.'test.txt'):'';
//        $content .= "-------------------------------\r\n";
//        $content .= time()."->pid:".posix_getpid().";sleep:".$sleep."a:".$a.";b:".$b."\r\n";
//        file_put_contents(ROOT_PATH.'test.txt',$content);
        return true;
    }
}