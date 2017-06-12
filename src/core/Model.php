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

namespace axios\tpr\core;

use think\Config;

class Model extends \think\Model{
    public function __construct($connection=[],$name='',$data = [])
    {
        parent::__construct($data);
        if(is_array($connection)){
            $connection = array_merge(Config::get('database'),$connection);
        }else if(is_string($connection)){
            $connection = Config::get('mysql.'.$connection);
            $connection = array_merge(Config::get('database'),$connection);
        }

        if(is_array($connection) && $this->connection!=$connection){
            $this->connection = $connection;
            $this->connect($this->connection);
        }

        if(!empty($name) && $this->name!=$name){
            $this->name = $name;
        }
        $this->table($connection['prefix'].$this->name);
    }
}