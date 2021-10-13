<?php

namespace ctaolee\swoole;


/***
 * Class Application
 * @package ctaolee\swoole
 * @property Request $request
 * @property Response $response
 */
class Application extends \yii\web\Application
{
    /**
     * 将 swoole 捕获的异常交给 ErrorHandler 处理
     * @return int
     */
    public function run()
    {
        try {
            $code = parent::run();
            if ($this->session->isActive)
                $this->session->close();
            return $code;
        }
        catch (\Exception $exception) {
            $this->errorHandler->handleException($exception);
            return 0;
        }
        catch (\Throwable $throwable) {
            $this->errorHandler->handleException($throwable);
            return 0;
        }
    }





    /**
     * 修改默认请求类与默认返回类
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'request' => ['class' => Request::class ],
            'response' => ['class' => Response::class ],
        ]);
    }
}