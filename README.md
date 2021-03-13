## 使用：
使用 composer 安装 `composer require ctaolee/yii2-swoole`
创建类似以下php脚本：
```php
<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/yii2/config/web.php';

$app = new ctaolee\swoole\Application($config);

$swooleConfig = [
    // ... 这里配置swoole
];
$server = new ctaolee\swoole\SwooleServer();
$server->run($app, '0.0.0.0', 9501, $swooleConfig);
```

