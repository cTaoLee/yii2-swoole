<?php
namespace ctaolee\swoole;

use Swoole\Process;
use Swoole\Http\Server;
use yii\base\BaseObject;

/**
 * Class SwooleServer
 * @package ctaolee\swoole
 */
class SwooleServer extends BaseObject
{
    private $_config;

    public function run($appConfig, $host, $port, $swooleConfig) {
        $this->_config = $appConfig;

        // 处理热重载配置
        if ($swooleConfig['hot_reload']) {
            $dir = $swooleConfig['inotify_files'];
            $pid = $swooleConfig['pid_file'];

            if (function_exists('inotify_init')) {
                $process = new Process(function() use ($dir, $pid) {
                    SwooleServer::hotReload($dir, $pid);
                });
                $process->start();
            }
            else {
                echo "开启热重载需要 inotify 扩展支持", PHP_EOL;
            }
        }
        unset($swooleConfig['hot_reload']);
        unset($swooleConfig['inotify_files']);

        $server = new Server($host, $port);
        $server->set($swooleConfig);
        $server->on('start', [$this,'onStart']);
        $server->on('WorkerStart', [$this,'onWorkerStart']);
        $server->on('request', [$this,'onRequest']);
        $server->start();
    }

    /**
     * 处理请求
     *
     * @param $request
     * @param $response
     * @throws
     */
    public function onRequest($request, $response) {
        $app = new Application($this->_config);
        $app->request->setSwRequest($request);
        $app->response->setSwResponse($response);
        $app->run();
    }



    public function onStart($server) {}
    public function onWorkerStart($server, $worker_id) {}

    public static function hotReload($dirs, $pidFile) {

        $events = [
            IN_MODIFY,
            IN_CREATE,
            IN_DELETE
        ];
        $my_event = array_sum($events);
        $ifd = inotify_init();
        foreach ($dirs as $dir) {
            static::inotifyAddWatchDir($ifd, $dir, $my_event);
        }
        stream_set_blocking($ifd, 1);
        while ($event_list = inotify_read($ifd)) {
            $pid = file_get_contents($pidFile);
            exec("kill -USR1 $pid");
        }
    }

    public static function  inotifyAddWatchDir($ifd, $dir, $event) {
        inotify_add_watch($ifd, $dir, $event);
        $files = scandir($dir);
        foreach ($files as $file) {
            $path = $dir .DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)  && $file != '.' && $file != '..') {
                static::inotifyAddWatchDir($ifd, $path, $event);
            }
        }
    }

}