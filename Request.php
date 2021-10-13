<?php

namespace ctaolee\swoole;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Cookie;
use yii\web\CookieCollection;
use yii\web\HeaderCollection;

/**
 * Class Request
 * @package ctaolee\swoole
 * @property \Swoole\Http\Request $swRequest
 */
class Request extends \yii\web\Request
{
    private $_swRequest;

    public function setSwRequest(\Swoole\Http\Request $request) {
        $this->_swRequest = $request;

        // 设置服务器参数
        foreach ($request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
        foreach ($request->header as $k => $v) {
            $_SERVER[ 'HTTP_' . strtoupper(str_replace([' ', '-'], ['_', '_'], $k))] = $v;
        }

        // 清空上次请求
        $this->getHeaders()->removeAll();
        $_COOKIE = [];
        $_SESSION = [];


        $this->setQueryParams($request->get);
        $this->setBodyParams($request->post);
        $this->setFiles($request->files);
        $this->setCookies($request->cookie);
        $this->setRawBody($request->rawContent() ?: null);
        $this->setPathInfo($request->server['path_info']);

    }

    public function getSwRequest() {
        return $this->_swRequest;
    }

    private function setCookies($cookies)
    {
        if (!$cookies) return null;
        foreach ($cookies as $name => $cookie) {
            $_COOKIE[$name] = $cookie;
        }
    }

    private function setFiles($files) {
        $_FILES = $files;
    }


    public function getHeaders()
    {
        $headers = new HeaderCollection();
        foreach ($this->swRequest->header as $k => $v) {
            $headers->add($k, $v);
        }
        $this->filterHeaders($headers);
        return $headers;
    }

    public function getMethod(){
        return $this->swRequest->getMethod();
    }

    public function getRawBody() {
        return $this->swRequest->rawContent() ?: null;
    }


}