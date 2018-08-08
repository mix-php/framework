<?php

namespace mix\base;

/**
 * 组件基类
 * @author 刘健 <coder.liu@qq.com>
 */
class Component extends BaseObject
{

    // 协程模式值
    const COROUTINE_MODE_NEW = 0;
    const COROUTINE_MODE_CLONE = 1;
    const COROUTINE_MODE_REFERENCE = 2;

    // 状态值
    const STATUS_READY = 0;
    const STATUS_RUNNING = 1;

    // 协程模式
    private $_coroutineMode = self::COROUTINE_MODE_NEW;

    // 状态
    private $_status = self::STATUS_READY;

    // 获取状态
    public function getStatus()
    {
        return $this->_status;
    }

    // 设置状态
    public function setStatus($status)
    {
        $this->_status = $status;
    }

    // 获取协程模式
    public function getCoroutineMode()
    {
        return $this->_coroutineMode;
    }

    // 设置协程模式
    public function setCoroutineMode($coroutineMode)
    {
        $this->_coroutineMode = $coroutineMode;
    }

    // 请求开始事件
    public function onRequestStart()
    {
        $this->setStatus(self::STATUS_RUNNING);
    }

    // 请求结束事件
    public function onRequestEnd()
    {
        $this->setStatus(self::STATUS_READY);
    }

}
