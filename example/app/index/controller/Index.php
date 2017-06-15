<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/16 10:23
 */
namespace tpr\index\controller;

use axios\tpr\core\Api;
use axios\tpr\service\ForkService;
use think\Log;
use tpr\index\service\TestService;

class Index extends Api {
    public function index(){
        $test = new TestService();
        ForkService::work($test,'test',[1,2]);
        Log::record('after_test','debug');
        $this->response(1);
    }
}