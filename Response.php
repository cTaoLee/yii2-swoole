<?php

namespace ctaolee\swoole;


class Response extends \yii\web\Response
{
    /**
     * @var \swoole_http_response $response
     */
    public $response;

    private $_headers;


    function sendHeaders()
    {
        if ($this->_headers) {
            foreach ($this->getHeaders() as $name => $values) {
                $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
                foreach ($values as $value) {
                    $this->response->header($name, $value);
                }
            }
        }
        $statusCode = $this->getStatusCode();
        $this->response->status($statusCode);
    }


    function sendContent()
    {
        $this->response->end($this->content);
    }


}