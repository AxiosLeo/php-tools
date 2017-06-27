<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/16 10:23
 */
namespace example\index\controller;

use axios\tpr\core\Api;
use axios\tpr\service\ForkService;

class Index extends Api {
    /**
     * 多行注释
     * @desc 一行写不下;
     * @desc 那就两行
     */
    public function index(){
//        $Test = new \example\index\service\Test();
//        for($i = 0; $i<10 ; $i++){
//            ForkService::work($Test,'files',[]);
//        }
        $this->response('hello,world!');
    }

    public function forkTest(){
        $this->response();
    }
}