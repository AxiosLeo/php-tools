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

use axios\tpr\service\LangService;
use function Sodium\crypto_box_seed_keypair;
use think\Response;
use think\Env;

final class Result{
    public static $instance;

    public static $return_type;

    public function __construct($return_type)
    {
        // TODO: Implement __call() method.
        if(empty($return_type)){
            self::initReturnType();
        }else{
            self::$return_type = $return_type;
        }
    }

    public static function instance($return_type = ""){
        if (is_null(self::$instance)) {
            self::$instance = new static($return_type);
        }
        return self::$instance;
    }

    public static function wrong($code,$message=''){
        return self::rep([],$code,$message);
    }

    public static function rep($data=[],$code=200,$message='',array $header=[]){
        $req['code'] = strval($code);
        $req['data'] = $data;
        $req['message'] = !empty($message)?LangService::trans($message):LangService::message($code);
        self::send($req,$header);
        return $req;
    }

    public static function send($req,$header=[]){
        if(empty(self::$return_type)){
            self::initReturnType();
        }
        Response::create($req,  self::$return_type, "200")->header($header)->send();
        if(function_exists('fastcgi_finish_request')){
            fastcgi_finish_request();
        }
    }

    private static function initReturnType(){
        $return_type = Env::get('api.return_type','json');
        self::$return_type = $return_type;
    }
}