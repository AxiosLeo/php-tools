<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/3/27 15:41
 */
namespace axios\tpr\exception;

use Exception;
use think\Env;
use think\Exception\Handle;
use think\Response;

class Http extends Handle{
    public function render(Exception $e)
    {
        //TODO::开发者对异常的操作
        //可以在此交由系统处理
        if(Env::get('debug.status')){
            return parent::render($e);
        }else{
            $req['code']= "500";
            $req['message'] = "something error";
            $req['data'] = [];
            $return_type = Env::get('response.return_type');
            if(empty($return_type)){
                $return_type = "json";
            }
            Response::create($req,$return_type,"500")->send();
            die();
        }
    }
}