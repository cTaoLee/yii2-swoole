<?php

namespace ctaolee\swoole;

/**
 * Class Request
 * @package ctaolee\swoole
 * @property \Swoole\Http\Request $request
 */
class Request extends \yii\web\Request
{
    private $_swRequest;

    public function setSwRequest($request) {
        $this->_swRequest = $request;
    }
    public function getSwRequest() {
        return $this->_swRequest;
    }

}