<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/5/12 16:28
 */
namespace axios\tpr\server;

use axios\tpr\service\RedisService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

/**
 * Class Server
 * @package axios\tpr\server
 */
class Server extends Command {
    protected $server;

    protected function configure()
    {
        parent::configure();
        $this->setName('server')
            ->addOption('server',null,Option::VALUE_NONE,'intput the name of server')
            ->setDescription('Start Gearman Server');
        $this->addArgument('server', Argument::REQUIRED, "The name of the server");

        $this->addArgument('fork', Argument::OPTIONAL, "The number of the subprocess");
    }

    protected function execute(Input $input, Output $output){
        $server = strtolower(trim($input->getArgument('server')));
        $fork = trim($input->getArgument('fork'));
        if($fork==="view"){
            $fork_process = RedisService::redis()->switchDB(0)->setsMembers($server);
            echo "child process:".count($fork_process)."\n";
            foreach ($fork_process as $f){
                echo "pid :".$f."; work \n";
            }
            die();
        }else if($fork === "stop"){
            $fork_process = RedisService::redis()->switchDB(0)->setsMembers($server);
            foreach ($fork_process as $f){
                echo "Stop $f:";
                if(posix_kill($f,SIGTERM)){
                    echo "success!\n";
                    RedisService::redis()->switchDB(0)->sRem($server,$f);
                }else{
                    echo "fail!\n";
                    RedisService::redis()->switchDB(0)->sRem($server,$f);
                }
            }
            posix_kill(posix_getpid(),SIGTERM);
            die();
        }

        $fork = isset($fork)&&is_numeric($fork)?intval($fork):1;

        $class  = ucfirst($server)."Service";
        RedisService::redis()->switchDB(0)->set($server."_pid",getmypid());
        RedisService::redis()->switchDB(0)->set($server."_fork",intval($fork));
        echo "PID:".getmypid()."\n";

        while (!empty(RedisService::redis()->switchDB(0)->get($server."_fork"))){
            $pid = pcntl_fork();
            if ($pid == -1) {//fail
                echo "could not fork";
                die();
            } else if ($pid) {//father
                RedisService::redis()->switchDB(0)->sAdd($server,$pid);
                RedisService::redis()->switchDB(0)->decr($server."_fork");
                echo "child pid:".$pid."\n";
                pcntl_waitpid( $pid , $status ,WNOHANG);
            } else { //child
                //Installing signal handler
                pcntl_signal(SIGHUP,  function ($signo) use ($server){
                    $pid = getmypid();
                    RedisService::redis()->switchDB(0)->sRem($server,$pid);
                    posix_kill($pid,SIGTERM);
                });

                /*** do something ***/
                self::runServer($class);
                sleep(10);

                //Dispatching
                posix_kill(posix_getpid(), SIGHUP);
                pcntl_signal_dispatch();
                die();
            }
        }

        self::showForkProcess($server);
        die();
    }

    public static function showForkProcess($server){
        $fork_process = RedisService::redis()->switchDB(0)->setsMembers($server);
        echo "child process:".count($fork_process)."\n";
        foreach ($fork_process as $f){
            echo "pid :".$f."; work \n";
        }
    }

    public static function runServer($class){
        call_user_func([$class,"run"]);
    }
}