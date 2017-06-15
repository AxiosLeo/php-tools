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
 namespace axios\tpr\service;

 use think\Hook;

 class ForkService{
     public static $class;

     public static function check(){
         return function_exists('pcntl_fork') && function_exists('posix_kill');
     }

     public static function fork($killFather=false){
         if(self::check()){
             Hook::add('request_done', 'axios\\tpr\\behavior\\RequestEnd');
             $pid = pcntl_fork();
             pcntl_signal(SIGHUP,  function (){
                 $pid = posix_getpid();
                 posix_kill($pid,SIGTERM);
                 exit();
             });
             if($pid>0){
                 pcntl_wait($status);
                 if($killFather){
                     exit();
                 }
                 return true;
             }else if($pid==0){
                 $ppid = pcntl_fork();
                 if($ppid>0){
                     posix_kill(posix_getpid(), SIGINT);
                     exit();
                 }else if($ppid == -1){
                     exit();
                 }
             }else{
                 return false;
             }
         }
         return false;
     }

     public static function work($class,$func,$args=[])
     {
         if(self::check()){
             $fork = self::fork();
             if($fork===true){
                 return true;
             }
             call_user_func_array([$class,$func],$args);
             posix_kill(posix_getpid(), SIGINT);
             exit();
         }
         return false;
     }
 }