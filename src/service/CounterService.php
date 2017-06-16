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

class CounterService {
    public static function createMemoryCounter($pathname,$type,$init=0){
        $shm = ftok($pathname, $type);
        $shm_id = shmop_open($shm,'c',0644,8);
        $size = $init;
        $size_save = str_pad($size,8,"0",STR_PAD_LEFT);
        shmop_write($shm_id,$size_save,0);
        return $size;
    }

    public static function incMemoryCounter($pathname,$type){
        $shm = ftok($pathname, $type);
        $shm_id = shmop_open($shm,'c',0644,8);
        $size = shmop_read($shm_id,0,8);
        $size = empty($size)?1:intval($size);
        $size = $size + 1;
        $size_save = str_pad($size,8,"0",STR_PAD_LEFT);
        shmop_write($shm_id,$size_save,0);
        return $size;
    }

    public static function decMemoryCounter($pathname,$type){
        $shm = ftok($pathname, $type);
        $shm_id = shmop_open($shm,'c',0644,8);
        $size = shmop_read($shm_id,0,8);
        $size = empty($size)?1:intval($size);
        $size = $size - 1;
        $size_save = str_pad($size,8,"0",STR_PAD_LEFT);
        shmop_write($shm_id,$size_save,0);
        return $size;
    }

    public static function getMemoryCounter($pathname,$type){
        $shm = ftok($pathname, $type);
        $shm_id = shmop_open($shm,'c',0644,8);
        $size = shmop_read($shm_id,0,8);
        $size = empty($size)?0:intval($size);
        return $size;
    }
}