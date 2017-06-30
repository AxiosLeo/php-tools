<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/16 10:40
 */
return [
    // 应用调试模式
    'app_debug'              => \think\Env::get('global.debug'),
    // 异常处理handle类 留空使用
    'exception_handle'       => '\\axios\\tpr\\core\\Http',

    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => false,

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => ['error','debug'],
        //MongoDB的连接配置
        'connection'=>'default',
        //默认日志数据库名称
        'database'=>'log',
        //日志时间日期格式
        'time_format'=>"Y-m-d H:i:s",
        //独立记录的日志级别
        'apart_level' => [],
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
];