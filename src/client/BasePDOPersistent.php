<?php

namespace mix\client;

/**
 * BasePdoPersistent组件
 * @author 刘健 <coder.liu@qq.com>
 */
class BasePDOPersistent extends BasePDO
{

    // 重新连接
    protected function reconnect()
    {
        $this->disconnect();
        $this->connect();
    }

    // 执行前准备
    protected function prepare()
    {
        try {
            // 执行前准备
            parent::prepare();
        } catch (\Throwable $e) {
            if (self::isDisconnectException($e)) {
                // 断开连接异常处理
                $this->reconnect();
                $this->prepare();
            } else {
                // 抛出其他异常
                throw $e;
            }
        }
    }

    // 开始事务
    public function beginTransaction()
    {
        try {
            // 执行前准备
            return parent::beginTransaction();
        } catch (\Throwable $e) {
            if (self::isDisconnectException($e)) {
                // 断开连接异常处理
                $this->reconnect();
                return $this->beginTransaction();
            } else {
                // 抛出其他异常
                throw $e;
            }
        }
    }

    // 判断是否为断开连接异常
    protected static function isDisconnectException(\Throwable $e)
    {
        $disconnectMessages = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
            'failed with errno',
        ];
        $errorMessage       = $e->getMessage();
        foreach ($disconnectMessages as $message) {
            if (false !== stripos($errorMessage, $message)) {
                return true;
            }
        }
        return false;
    }

}
