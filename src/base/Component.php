<?php

namespace mix\base;

/**
 * 组件基类
 * @author 刘健 <coder.liu@qq.com>
 */
class Component extends BaseObject
{

    // 组件状态值
    const STATUS_READY = 0;
    const STATUS_RUNNING = 1;

    // 协程模式值
    const COROUTINE_MODE_COMMON = 0;
    const COROUTINE_MODE_CLONE = 1;
    const COROUTINE_MODE_NEW = 2;

    // 协程模式
    private $_coroutineMode;

    // 组件状态
    private $_status;

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

    // 获取状态
    public function getCoroutineMode()
    {
        return $this->_coroutineMode;
    }

    // 设置协程模式
    public function setCoroutineMode($coroutineMode)
    {
        $this->_coroutineMode = $coroutineMode;
    }

    // 初始化事件
    public function onInitialize()
    {
        parent::onInitialize();
        $this->setStatus(self::STATUS_READY);
        $this->setCoroutineMode(self::COROUTINE_MODE_NEW);
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
