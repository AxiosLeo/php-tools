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

 class ForkService{
     public static $size = 0;

     public static $queue = [];

     protected static $pid_list = [];

     public static function check(){
         return function_exists('pcntl_fork') && function_exists('posix_kill') && function_exists('ftok') && function_exists('shmop_open') ;
     }

     public static function doFork($queue=[]){
         $max = EnvService::get('global.max_process',100);
         CounterService::incMemoryCounter(__FILE__,'h');
         foreach ($queue as $q){
             $size = CounterService::getMemoryCounter(__FILE__,'h');
             while($size>=$max) {
                 sleep(3);
                 $size = CounterService::getMemoryCounter(__FILE__,'h');
             };
             self::doWork($q['class'],$q['func'],$q['args']);
         }
         CounterService::decMemoryCounter(__FILE__,'h');
     }

     public static function doWork($class,$func,$args=[]){
         if(is_string($class) && class_exists($class)){
             $class = new $class();
         }
         if(self::check()){
             $fork = self::fork();
             if($fork){
                 return $fork;
             }
             call_user_func_array([$class,$func],$args);
             CounterService::decMemoryCounter(__FILE__,'h');
             posix_kill(posix_getpid(), SIGINT);
             exit();
         }
         return false;
     }

     public static function fork($killFather=false){
         if(self::check()){
             $pid = pcntl_fork();
             if($pid>0){
                 pcntl_wait($status);
                 if($killFather){
                     exit();
                 }
                 return $pid;
             }else if($pid==0){
                 $ppid = pcntl_fork();
                 if($ppid>0){
                     posix_kill(posix_getpid(), SIGINT);
                     exit();
                 }else if($ppid == -1){
                     exit();
                 }
                 ToolService::identity(2);
                 if(!$killFather){
                     CounterService::incMemoryCounter(__FILE__,'h');
                 }

                 return false;
             }else{
                 return false;
             }
         }
         return false;
     }

     public static function work($class,$func,$args=[])
     {
         $queue = [
             'class'=>$class,
             'func'=>$func,
             'args'=>$args
         ];
         array_push(self::$queue,$queue);
         return true;
     }
 }