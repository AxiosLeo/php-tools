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
use think\Env;
use think\Log;

class SignService {
    public function checkSign($post_timestamp,$post_sign){
        $api_key = Env::get('auth.api_key');
        if(empty($api_key)){
            return 500100;
        }
        $sign = $this->makeSign($post_timestamp,$api_key);
        $result = $post_sign!=$sign?false:true;
        if(!$result){
            Log::info(['post_timestamp'=>$post_timestamp,'need'=>$sign]);
        }
        return $result;
    }

    /**
     * 生成签名示例方法，建议自定义生成规则
     * @param $timestamp
     * @param $api_key
     * @return string
     */
    private function makeSign($timestamp,$api_key){
        return md5($timestamp.$api_key);
    }
}