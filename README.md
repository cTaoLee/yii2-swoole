## 使用：
使用 composer 安装 `composer require ctaolee/yii2-swoole`
创建类似以下php脚本：
```php
<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/web.php';

$swooleConfig = [
    // ... 这里配置swoole
];
$server = new ctaolee\swoole\SwooleServer();
$server->run($config, '0.0.0.0', 9501, $swooleConfig);
```

## 热重载
需要安装 **inotify** 扩展，可以通过 **pecl** 来安装，同时需要添加类似以下配置
```php
$swooleConfig = [
    // ... 其他配置
    
    'hot_reload' => true,                               // 开启热重载
    'inotify_files' => [
        __DIR__ . '/models',
        __DIR__ . '/modules',
        __DIR__ . '/config',
    ],
    'pid_file' => __DIR__ . '/runtime/swoole.pid',      // pid文件位置，必须配置
    
    
];
```
## Session
只实现了 redis-session
```php
[
    // ... 其他配置
    'components' => [
        // ... 其他配置
         'session' => [
            'class' => 'ctaolee\swoole\session\RedisSession',
            'redis' => 'redis',
            'timeout' => 3600,
        ],
    ]
];
```
