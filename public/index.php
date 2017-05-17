<?php

// [ 应用入口文件 ]

// 定义应用目录
define('ROOT_PATH',__DIR__.'/../');
define('APP_PATH', ROOT_PATH . 'example/app/');
define('CONF_PATH', ROOT_PATH.'example/config/');
define('RUNTIME_PATH', ROOT_PATH . 'example/runtime/');
define('APP_NAMESPACE','tpr');

// 加载框架引导文件
require __DIR__ . '/../vendor/topthink/framework/start.php';
