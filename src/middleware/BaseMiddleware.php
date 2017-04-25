<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/4/24 13:58
 */
namespace axios\tpr\middleware;

use axios\tpr\service\GlobalService;

class BaseMiddleware {
    protected $param ;

    protected $method ;

    protected $app_key ;

    protected $identify ;

    function __construct()
    {
        $this->param = GlobalService::get('param');
        $this->method= GlobalService::get('method');
        $this->app_key = GlobalService::get('app_key');
        $this->identify = GlobalService::get('identify');
    }

    protected function get($name='param'){
        return GlobalService::get($name);
    }
}