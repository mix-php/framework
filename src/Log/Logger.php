<?php

namespace Mix\Log;

use Mix\Helpers\JsonHelper;
use Mix\Core\Component;
use Mix\Core\ComponentInterface;

/**
 * Logger组件
 * @author 刘健 <coder.liu@qq.com>
 */
class Logger extends Component
{

    // 协程模式
    public static $coroutineMode = ComponentInterface::COROUTINE_MODE_REFERENCE;

    // 日志记录级别
    public $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

    /**
     * 处理者
     * @var \Mix\Log\FileHandler
     */
    public $handler;

    // emergency日志
    public function emergency($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    // alert日志
    public function alert($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    // critical日志
    public function critical($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    // error日志
    public function error($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    // warning日志
    public function warning($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    // notice日志
    public function notice($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    // info日志
    public function info($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    // debug日志
    public function debug($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    // 记录日志
    public function log($level, $message, array $context = [])
    {
        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        if (!in_array($level, $levels) || in_array($level, $this->levels)) {
            return $this->handler->write($level, $message, $context);
        }
        return false;
    }

}
