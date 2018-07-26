<?php

namespace mix\client;

/**
 * BaseRedisPersistent组件
 * @author 刘健 <coder.liu@qq.com>
 */
class BaseRedisPersistent extends BaseRedis
{

    // 重用连接(相同配置)
    public $reusableConnection = false;

    // 初始化
    protected function initialize()
    {
        // 重用连接(相同配置)
        if ($this->reusableConnection) {
            $hash         = md5($this->host . $this->port . $this->database . $this->password);
            $this->_redis = &\Mix::$container['redis_' . $hash];
        }
    }

    // 重新连接
    protected function reconnect()
    {
        $this->disconnect();
        $this->connect();
    }

    // 执行命令
    public function __call($name, $arguments)
    {
        try {
            // 更新活动时间
            $this->_lastActiveTime = time();
            // 执行命令
            return parent::__call($name, $arguments);
        } catch (\Throwable $e) {
            if (self::isDisconnectException($e)) {
                // 断开连接异常处理
                $this->reconnect();
                return $this->__call($name, $arguments);
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
            'failed with errno',
            'connection lost',
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
