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

namespace axios\tpr\behavior;

use axios\tpr\service\ForkService;
use axios\tpr\service\ToolService;
use think\Request;

/**
 * Class LogWriteDone
 * @package axios\tpr\behavior
 *
 * need library/think/Log.php 161
 *  ->   Hook::listen('log_write_done', $log);
 */
class LogWriteDone extends ForkService{
    public $param;
    public $request;
    function __construct()
    {
        $this->request = Request::instance();
        $this->param = $this->request->param();
    }

    public function run(){
        $identity = ToolService::identity();
        if($identity==2){
            posix_kill(posix_getpid(), SIGINT);
            exit();
        }
    }
}