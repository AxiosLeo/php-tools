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

use think\Log;

class TPLogService extends Log {
    public static $dir = [];
    public static $index = 0;
    public static function logData(){
        self::deepScanDir(LOG_PATH);
        $log_dir = rtrim(LOG_PATH, '//');
        unset(self::$dir[$log_dir]);
        $dir = [];$n=0;
        foreach (self::$dir as $k=>$d){
            $dir[$n]['dir'] = $k;
            $date = basename($k);
            $temp = [];$t=0;
            foreach ($d as $f){
                $temp[$t]['file_dir'] = $f;
                $temp[$t]['file_name'] = basename($f);
                $file = explode(".",$temp[$t]['file_name']);
                $day = isset($file[0])?$file[0]:"";
                $temp[$t]['log_date'] = $date.$day;
                $temp[$t]['log'] = self::analysisLog($f);
                $t++;
            }
            $dir[$n]['file']= $temp;
            $n++;
        }
        return $dir;
    }

    public static function analysisLog($file_path){
        if(file_exists($file_path)){
            $content = file_get_contents($file_path);
            $split = explode("---------------------------------------------------------------",$content);
            $logs = [];
            foreach ($split as $k=>$l){
                if($l==""){
                    continue;
                }
                $logs[$k] = self::analysisLogRow($l);
            }
            return $logs;
        }else{
            return [];
        }
    }

    protected static function analysisLogRow($log_content){
        $log_content = str_replace("\r\n[","\r\n@@[",$log_content);
        $temp = explode("\r\n@@",$log_content);
        $log =[];$n=0;
        foreach ($temp as $t){
            if(empty($t)|| $t=="\n"){
                continue;
            }
            if($n===0){
                $log['datetime'] = substr($t,2,25);
                $first_temp =explode(" ",substr($t,30));
                $log['client_ip'] = isset($first_temp[0])?$first_temp[0]:"";
                $log['server_ip'] = isset($first_temp[1])?$first_temp[1]:"";
                $log['method'] = isset($first_temp[2])?$first_temp[2]:"";
                $log['path'] = isset($first_temp[3])?$first_temp[3]:"";
                $n++;
                continue;
            }
            $pos0 = strpos($t,"[")+2;
            $pos1 = strpos($t,"]")-1;
            $type = substr($t,$pos0,$pos1-$pos0);
            $content = substr($t,$pos1+3);

            if($n===1) {
                $pos0 = strpos($content,"[")+16;
                $pos1 = strpos($content,"]")-1;
                $run_time = substr($content,$pos0,$pos1-$pos0);
                $content = substr($content,$pos1+2);
                $log['run_time'] = $run_time;

                $pos0 = strpos($content,"[")+13;
                $pos1 = strpos($content,"]");
                $log['throughput'] = substr($content,$pos0,$pos1-$pos0);
                $content = substr($content,$pos1+2);

                $pos0 = strpos($content,"[")+16;
                $pos1 = strpos($content,"]");
                $log['memory'] = substr($content,$pos0,$pos1-$pos0);
                $content = substr($content,$pos1+2);

                $pos0 = strpos($content,"[")+16;
                $pos1 = strpos($content,"]");
                $log['load_file'] = substr($content,$pos0,$pos1-$pos0);
            }else{
                if($type == 'info'){
                    $pos0 = strpos($content,"[")+2;
                    $pos1 = strpos($content,"]")-1;
                    $info_type = substr($content,$pos0,$pos1-$pos0);
                    $content = substr($content,$pos1+3);
                    $log[$type][$info_type] = $content;
                }else{
                    $log[$type] = $content;
                }
            }
            $n++;
        }
        return $log;
    }

    private static function deepScanDir($dir) {
        $dir = rtrim($dir, '//');
        if ( is_dir($dir)&&$dir != LOG_PATH) {
            self::$dir[$dir] = [];
            $dirHandle = opendir($dir);
            while (false !== ($fileName = readdir($dirHandle))) {
                $subFile = $dir . DS . $fileName;
                if (is_file($subFile)) {
                    $count = count(self::$dir[$dir]);
                    self::$dir[$dir][$count] = $subFile;
                }
                elseif (is_dir($subFile) && str_replace('.', '', $fileName) != '') {
                    self::deepScanDir($subFile);
                }
            }
            closedir($dirHandle);
        }else if (is_dir($dir)&&$dir == LOG_PATH){
            self::deepScanDir($dir);
        }
    }
}