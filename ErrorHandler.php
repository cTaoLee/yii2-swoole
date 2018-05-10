<?php
/**
 * Created by PhpStorm.
 * User: ctaolee
 * Date: 2018/5/6
 * Time: 上午1:58
 */

namespace ctaolee\swoole;

use Yii;
use yii\base\ErrorException;

class ErrorHandler extends \yii\web\ErrorHandler
{
    public function renderException($exception)
    {
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }
        $response->data = $this->renderFile(
         	'@yii/views/errorHandler/exception.php',
         	[ 'exception' => $exception,]
         );
        $response->send();
    }
}