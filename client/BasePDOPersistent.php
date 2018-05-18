<?php

namespace mix\client;

/**
 * BasePdoPersistent组件
 * @author 刘健 <coder.liu@qq.com>
 */
class BasePDOPersistent extends BasePDO
{

    // 重用连接(相同配置)
    public $reusableConnection = false;

    // 初始化
    protected function initialize()
    {
        // 重用连接(相同配置)
        if ($this->reusableConnection) {
            $hash       = md5($this->dsn . $this->username . $this->password);
            $this->_pdo = &\Mix::$container['pdo_' . $hash];
        }
    }

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
        } catch (\Exception $e) {
            if (self::isDisconnectException($e)) {
                // 连接异常处理
                $this->reconnect();
                $this->prepare();
            } else {
                // 抛出其他异常
                throw $e;
            }
        }
    }

    // 判断是否为连接异常
    protected static function isDisconnectException(\Exception $e)
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
