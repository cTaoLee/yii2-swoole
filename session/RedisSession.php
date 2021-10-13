<?php


namespace ctaolee\swoole\session;

use Yii;
use yii\redis\Session;

class RedisSession extends Session
{

    public function getId()
    {
        if( isset($_COOKIE[$this->getName()]) ){
            $id = $_COOKIE[$this->getName()];
        }else{
            $id = uniqid();
        }
        return $id;
    }




    public function readSession($id)
    {
        $id = $this->getId();
        $data = $this->redis->executeCommand('GET', [$this->calculateKey($id)]);
        return $data === false || $data === null ? '' : $data;
    }


    public function writeSession($id, $data)
    {
        $id = $this->getId();
        if ($this->getUseStrictMode() && $id === $this->_forceRegenerateId) {
            return true;
        }
        return (bool) $this->redis->executeCommand('SET', [$this->calculateKey($id), $data, 'EX', $this->getTimeout()]);
    }

    public function destroySession($id)
    {
        $id = $this->getId();
        $this->redis->executeCommand('DEL', [$this->calculateKey($id)]);
        return true;
    }


}