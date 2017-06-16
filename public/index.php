<?php

// [ 应用入口文件 ]

// 定义应用目录
define('ROOT_PATH',__DIR__.'/../');
define('APP_PATH', ROOT_PATH . 'example/app/');
define('CONF_PATH', ROOT_PATH.'example/config/');
define('RUNTIME_PATH', ROOT_PATH . 'example/runtime/');
define('APP_NAMESPACE','tpr');
define('THINK_PATH',ROOT_PATH."vendor/topthink/framework/");

// 加载Behavior
require_once THINK_PATH."library/think/Hook.php";
\think\Hook::add('app_init' ,'axios\\tpr\\behavior\\AppInit');
\think\Hook::add('action_begin' ,'axios\\tpr\\behavior\\ActionBegin');
\think\Hook::add('app_end' ,'axios\\tpr\\behavior\\AppEnd');
\think\Hook::add('log_write_done', 'axios\\tpr\\behavior\\LogWriteDone');

// 加载公共语言包路径
define('LANG_PATH',CONF_PATH.'lang/');

//引入公共方法文件
include_once CONF_PATH.'common.php';

// 加载框架引导文件
require THINK_PATH.'start.php';
