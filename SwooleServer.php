<?php
namespace ctaolee\swoole;

use Swoole\Http\Server;
use yii\base\BaseObject;

/**
 * Class SwooleServer
 * @package ctaolee\swoole
 */
class SwooleServer extends BaseObject
{
    /* @var Application $_app */
    private $_app;


    public function run( Application $app, $host, $port, $config) {
        $this->_app = $app;

        $server = new Server($host, $port);
        $server->set($config);
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
     */
    public function onRequest($request, $response) {
        $this->setAppRunEnv($this->_app, $request, $response);
        $this->_app->run();
    }

    /**
     * 为应用设置 swoole 请求与返回
     *
     * @param Application $app
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public function setAppRunEnv($app, $request, $response) {

        $app->request->setSwRequest($request);
        $app->response->setSwResponse($response);

        // 清除上个请求的数据
        $app->request->getHeaders()->removeAll();
        $app->response->clear();

        // 设置服务器参数
        foreach ($request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
        // 设置请求头
        foreach ($request->header as $name => $value) {
            $app->request->getHeaders()->set($name, $value);
        }
        // 设置请求参数
        $app->request->setQueryParams($request->get);
        $app->request->setBodyParams($request->post);
        $rawContent = $request->rawContent() ?: null;
        $app->request->setRawBody($rawContent);
        // 设置路由
        $app->request->setPathInfo($request->server['path_info']);

    }

    public function onStart($server) {}
    public function onWorkerStart($server, $worker_id) {}

}