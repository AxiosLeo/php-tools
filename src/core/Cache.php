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

namespace axios\tpr\core;

use think\Cache as TCache;
use think\Config;
use think\Request;

class Cache {
    public static function set($req,Request $request){
        $config = Config::get('cache');
        if(isset($config['list'][$request->path()])){
            $ip = get_client_ip();
            $expire = $config['list'][$request->path()];
            $expire = $expire?$expire:300;
            $param = $request->except($config['except_param']);
            $identify = md5($ip.serialize($param));
            TCache::set($identify,$req,$expire);
        }
    }

    public static function get(Request $request){
        $config = Config::get('cache');
        if(isset($config['list'][$request->path()])){
            $ip = get_client_ip();
            $param = $request->except($config['except_param']);
            $identify = md5($ip.serialize($param));
            $cache =  TCache::get($identify);
            return empty($cache)?false:$cache;
        }else{
            return false;
        }
    }
}