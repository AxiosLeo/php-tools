<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/16 17:18
 */
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