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
use axios\tpr\service\ApiDocService;
use axios\tpr\service\ForkService;

class Index extends Api {
    /**
     * 测试接口
     * @desc
     * @method post | get
     * @parameter 参数类型 参数名称
     * @response string param_name param_info description
     * @a this is a
     * @b this is b
     * @array this is array 1
     * @array this is array 2
     * @multiRow this is ;
     * @multiRow multiRow comment
     * @multiRowParam string multi_row_param this is ;
     * @multiRowParam multi_row_param comment
     * @multiRowParam string multi_row;
     * @multiRowParam _param2 this is multi_row_param comment
     * @test void|string asdf
     */
    public function index(){
        ApiDocService::config(__DIR__);
        dump(ApiDocService::makeClassDoc(__CLASS__));
        $this->response('hello,world!');
    }

    public function forkTest(){
        $this->response();
    }
}