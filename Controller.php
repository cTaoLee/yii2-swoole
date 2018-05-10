<?php

namespace ctaolee\swoole;
use yii\web\Application;

/**
 * Manages Swoole Server.
 * @package ctaolee\swoole
 * @property-read \swoole_http_server server
 * @property-read int pid
 * @property-read array config
 */
class Controller extends \yii\console\Controller
{
    public $defaultAction = "info";

    public function getConfig(){
        return include \Yii::getAlias("@app/config/swoole.php");
    }

    public function getServer(){
        $config = $this->config;
        $serv = new \swoole_http_server($config['host'], $config['port']);
        $serv->set($config);

        $serv->on('request', function(\swoole_http_request $req, \swoole_http_response $res) use ($serv){
            if($this->config['hotModulReplacement'] == 1)
                $serv->reload();
            $config = include \Yii::getAlias("@app/config/web.php");
            $config['components']['request']  = ['class' => Request::class,  'request'  => $req ];
            $config['components']['response'] = ['class' => Response::class, 'response' => $res];
            $config['components']['errorHandler'] = ['class' => ErrorHandler::class ];
            $app = new Application($config);

            try{
                $app->run();
            }
            catch (\Exception $exception){
                /** @var ErrorHandler $errorHandler */
                $errorHandler = $app->errorHandler;
                $errorHandler->renderException($exception);
            }
        });
        return $serv;
    }

    public function getPid(){
        $pid_file = $this->config['pid_file'];
        if(file_exists($pid_file)){
            $pid = file_get_contents($pid_file);
            if (posix_getpgid($pid)) {
                return $pid;
            } else {
                unlink($pid_file);
            }
        }
        return false;
    }

    public function actionStart(){
        if($this->pid){
            die( "server is running...\n");
        }
        else{
            echo"server start...", PHP_EOL;
            $this->server->start();
        }
    }

    public function actionStop(){
        if($this->pid){
            $pid_file = $this->config['pid_file'];
            posix_kill($this->pid, SIGTERM);
            unlink($pid_file);
            die( "server is stop .\n" );
        }
        die( "server is not running .\n");
    }

    public function actionRestart(){
        $pid = $this->pid;
        if($pid){
            posix_kill($pid, SIGTERM);
        }
        $time = 0;

        while(posix_getpgid($pid) == $pid && $time++ < 10){
            usleep(10000);
        }

        if($time < 10){
            echo "server is stop .", PHP_EOL;
            $this->actionStart();
        }
        else{
            die("server stop timeout .\n");
        }
    }

    public function actionInfo(){
        if($this->pid){
            die("server is running...\n");
        }
        else{
            die("server is stop .\n");
        }
    }

}