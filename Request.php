<?php

namespace ctaolee\swoole;

use Yii;
use yii\web\NotFoundHttpException;

class Request extends \yii\web\Request
{

    public $request;

    /**
     * 路由的解析
     * @return array
     * @throws NotFoundHttpException
     */
    public function resolve()
    {
        $result = Yii::$app->getUrlManager()->parseRequest($this);
        if ($result !== false) {
            list ($route, $params) = $result;
            if ($this->_queryParams === null) {
                $this->setQueryParams( array_merge($params, $this->getQueryParams()) ); // preserve numeric keys
            } else {
                $this->_queryParams = $params + $this->_queryParams;
            }
            return [$route, $this->getQueryParams()];
        }
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }



    private $_bodyParams;
    /**
     * 获取 post 数据
     * @return array|null
     */
    function getBodyParams(){
        if ($this->_bodyParams === null) {
            if($this->method === "POST")
                $this->_bodyParams = $this->request->post;
            else
                $this->_bodyParams = [];
        }
        return $this->_bodyParams;
    }

    private $_queryParams;
    /**
     * 获取 get 数据
     * @return array|null
     */
    public function getQueryParams()
    {
        if ($this->_queryParams === null) {
            $this->_queryParams = $this->request->get ?: [];
        }
        return $this->_queryParams;
    }



    public function resolveRequestUri(){
        return "";
    }
}