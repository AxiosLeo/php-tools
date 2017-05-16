<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/16 16:17
 */
namespace tpr\test\controller;

use axios\tpr\core\Api;

class A extends Api{
    public function index(){
//        $this->return_type = "xml";
        sleep(3);
        $this->response(time());
    }
}