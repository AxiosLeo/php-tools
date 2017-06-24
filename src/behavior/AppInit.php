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

use axios\tpr\service\ToolService;
use think\Lang;
use think\Request;
use think\Hook;

class AppInit {
    public $param;
    public $request;
    function __construct()
    {
        $this->request = Request::instance();
        $this->param = $this->request->param();
    }

    public function run(){
        Hook::add('action_begin' ,'axios\\tpr\\behavior\\ActionBegin');
        Hook::add('app_end' ,'axios\\tpr\\behavior\\AppEnd');
        Hook::add('log_write_done', 'axios\\tpr\\behavior\\LogWriteDone');
        Hook::add('request_end', 'axios\\tpr\\behavior\\RequestEnd');
        ToolService::identity(1);
        $this->lang();
    }

    public function lang(){
        Lang::detect();
        $this->request->langset(Lang::range());
        $lang_path = defined('LANG_PATH')?LANG_PATH:CONF_PATH.'lang'.DIRECTORY_SEPARATOR;
        $lastString = substr($lang_path,-1);
        if($lastString!=DS){
            $lang_path .= DS;
        }
        Lang::load($lang_path. $this->request->langset() . EXT);
    }

}