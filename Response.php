<?php

namespace ctaolee\swoole;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\CookieCollection;

/**
 * Class Response
 * @package ctaolee\swoole
 * @property \Swoole\Http\Response $swResponse
 */
class Response extends \yii\web\Response
{
    private $_swResponse;

    public function setSwResponse($response)
    {
        $this->_swResponse = $response;
    }
    public function getSwResponse()
    {
        return $this->_swResponse;
    }

    /**
     * 重构设置返回头
     */
    public function sendHeaders()
    {
        $headers = $this->getHeaders();
        $this->swResponse->status($this->getStatusCode());
        if ($headers->count > 0) {
            foreach ($headers as $name => $values) {
                $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
                foreach ($values as $value) {
                    $this->swResponse->header($name, $value);
                }
            }
        }
        $this->sendCookies();
    }

    /**
     * 重构设置返回内容
     */
    public function sendContent()
    {
        if ($this->stream === null) {
            if ($this->content) {
                $this->swResponse->end($this->content);
            } else {
                $this->swResponse->end();
            }
            return;
        }

        if (function_exists('set_time_limit')) {
            set_time_limit(0);
        } else {
            Yii::warning('set_time_limit() is not available', __METHOD__);
        }

        $chunkSize = 8 * 1024 * 1024;
        if (is_array($this->stream)) {
            list ($handle, $begin, $end) = $this->stream;
            fseek($handle, $begin);
            while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
                if ($pos + $chunkSize > $end) {
                    $chunkSize = $end - $pos + 1;
                }
                $this->swResponse->write(fread($handle, $chunkSize));
                flush();
            }
            fclose($handle);
        } else {
            while (!feof($this->stream)) {
                $this->swResponse->write(fread($this->stream, $chunkSize));
                flush();
            }
            fclose($this->stream);
        }
        $this->swResponse->end();
    }
    

    protected function sendCookies()
    {
        $request = Yii::$app->getRequest();
        if ($request->enableCookieValidation) {
            if ($request->cookieValidationKey == '') {
                throw new InvalidConfigException(get_class($request) . '::cookieValidationKey must be configured with a secret key.');
            }
            $validationKey = $request->cookieValidationKey;
        }
        foreach ($this->getCookies() as $cookie) {
            $value = $cookie->value;
            if ($cookie->expire != 1 && isset($validationKey)) {
                $value = Yii::$app->getSecurity()->hashData(serialize([$cookie->name, $value]), $validationKey);
            }

            $this->swResponse->cookie($cookie->name, $value, $cookie->expire, $cookie->path, $cookie->domain, $cookie->secure, $cookie->httpOnly);
        }
        $this->setSessionId();
    }


    private function setSessionId() {
        $request = Yii::$app->getRequest();
        $session = Yii::$app->session;
        $id = $request->getCookies()->get($session->getName());

        // 不存在时
        if (!$id) {
            $id = $session->getId();
            $data = $session->getCookieParams();
            $this->swResponse->cookie(
                $session->getName(), $id, $data['lifetime'],
                $data['path'], $data['domain'], $data['secure'],
                $data['httponly']
            );
        }
    }

}