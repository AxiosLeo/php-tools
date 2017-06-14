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

namespace axios\tpr\server;

use think\Config;
use think\Log;

class GearmanServer {
    public $worker;
    public $servers;
    public $config = [
        'servers' =>"127.0.0.1:4730",
        'job_name'=>"job",
        'timeout' =>20000
    ];
    public $jobName;
    public $receive = [
        'server'=>"",
        'func'=>"index",
        'data'=>[]
    ];
    function __construct()
    {
        $config = Config::get('gearman');
        $this->config = array_merge($this->config ,$config);
        $this->servers = $this->config['servers'];
        $this->jobName = $this->config['job_name'];

        $this->worker = new \GearmanWorker();
        $this->worker->addServers($this->servers);

        $count = 0;
        $this->worker->addFunction($this->jobName,'doJob',$count);
        $this->worker->setTimeout ($this->config['timeout']);
    }
    public function run(){
        while(@$this->worker->work()|| $this->worker->returnCode() == GEARMAN_TIMEOUT){
            if ($this->worker->returnCode() == GEARMAN_TIMEOUT)
            {
                Log::error("Gearman timeout! datetime:". date("Y-m-d H:i:s"));
                continue;
            }

            if ($this->worker->returnCode() != GEARMAN_SUCCESS) {
                Log::error("Gearman error! return_code:".$this->worker->returnCode());
            }
        }
    }
    function doJob(\GearmanJob $job,&$count){
        $handle = $job->handle();
        $timestamp = time();
        $datetime = date("Y-m-d H:i:s",$timestamp);
        $data = $job->workload();
        $receive = json_decode($data,true);
        $dataSize = $job->workloadSize();

        $this->receive = array_merge($this->receive,$receive);

        $class = middleware($this->receive['server'],'server');
        $func =  $this->receive['func'];

        $result = call_user_func_array([$class,$func], $this->receive['data']);

        $end_time = time();

        $log = [
            'count'=>$count,
            'handle'=>$handle,
            'timestamp'=>$timestamp,
            'timestamp_end'=>$end_time,
            'run_time'=>$end_time-$timestamp,
            'datetime'=>$datetime,
            'receive'=>$receive,
            'data_size'=>$dataSize,
            'result'=>$result
        ];

        Log::record($log,'gearman');
        $job->sendComplete(json_encode($log));
    }
}