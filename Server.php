<?php

namespace ctaolee\swoole;

class Server
{
    private $serv;

    public $workerStart;

    public function __construct($config = [])
    {

        $defaultConfig = [
            'host'          => 'localhsot',         // 地址
            'port'          => 9501,                // 端口
            'max_conn'      => 100,                 // 最大连接数
            'daemonize'     => true,                // 守护进程化
            'reactor_num'   => 4,                   // 线程数
            'worker_num'    => 1,                   // 进程数
            'max_request'   => 100,                 // 单个worker进程执行次数，超过则重新建立防止内存溢出
            'backlog'       => 128,                 // 等待队列长度
            'open_cpu_affinity' => 1,               // CPU亲和设置
            //'open_tcp_nodelay'  => 1,             // 启用tcp_nodelay
            //'tcp_defer_accept'  => 5,             // 5秒内并不会触发accept
            'log_file'      => '/project/php/yii/runtime/logs/swoole.log',
            'pid_file'      => '/project/php/yii/runtime/run/swoole.pid',
        ];
        $config += $defaultConfig;

        $serv = new \swoole_http_server($config['host'], $config['port']);
        $serv->set($config);
        $serv->on('request', [$this, "request"]);
        $this->serv = $serv;
    }



    public function request(){
        
    }


    public function start(){
        $this->serv->start();
    }
}


