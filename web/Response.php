<?php

namespace ctaolee\swoole\web;


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
                // set replace for first occurrence of header but false afterwards to allow multiple
                $replace = true;
                foreach ($values as $value) {
                    header("$name: $value", $replace);
                    $replace = false;
                }
            }
        }
        $statusCode = $this->getStatusCode();
        print_r($this->_headers);
        $this->response->status($statusCode);
    }


    function sendContent()
    {

        $this->response->end($this->content);
    }


    function send()
    {
        print_r($this->data);
        return parent::send();
    }

}