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
use axios\tpr\service\ApiDocService;

class Index extends Api {
    /**
     * 多行注释
     * @desc 一行写不下;
     * @desc 那就两行
     */
    public function index(){
//        $doc = ApiDocService::config(APP_PATH)->doc();
//        $doc = ApiDocService::doc();
//        dump($doc);
//        $doc = ApiDocService::makeClassDoc('tpr\index\controller\Index');
//        dump($doc);
//        $doc = ApiDocService::makeMethodDoc('tpr\index\controller\Index','index');
//        dump($doc);
        $this->response('hello,world!');
    }

    public function forkTest(){
        $this->response();
    }
}