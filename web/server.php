<?php

$http = new \swoole_http_server('127.0.0.1', 9501);



$http->on('request', function (\swoole_http_request $req, \swoole_http_response $res) use ($http){

    $http->reload();

    echo 'request', PHP_EOL;
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');


    $config = require(__DIR__ . '/../config/web.php');

    $config['components']['request'] = [
        'class' => app\swoole\src\Request::class,
        'request' => $req
    ];

    $config['components']['response'] = [
        'class' => app\swoole\src\Response::class,
        'response' => $res
    ];
    (new yii\web\Application($config))->run();



});

$http->on('WorkerStart', function(swoole_server $server, $worker_id){

    include_once(__DIR__ . '/../vendor/autoload.php');
    include_once(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

    echo "worker start", PHP_EOL;
    echo $worker_id, PHP_EOL;

});




$http->start();


