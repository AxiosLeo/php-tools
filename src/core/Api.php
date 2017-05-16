<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/16 15:58
 */
namespace axios\tpr\core;

use think\Controller;
use think\Request;

class Api extends Controller{
    protected $param;
    protected $return_type;
    function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->param = $this->request->param();
    }

    protected function wrong($code,$message=''){
        $this->rep([],$code,$message);
    }

    protected function rep($data=[],$code=200,$message='',array $header=[]){
        $req = Result::instance($this->return_type)->rep($data,$code,$message,$header);
        $this->request->req = $req;
        Cache::set($req,$this->request);
    }

    protected function response($data=[],$code=200,$message='',array $header=[]){
        $data = arrayDataToString($data);
        $this->rep($data,$code,$message,$header);
    }

    public function __empty(){
        $this->wrong(404);
    }
}