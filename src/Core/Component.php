<?php

namespace Mix\Core;

/**
 * 组件基类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
abstract class Component extends DIObject implements ComponentInterface
{

    // 协程模式
    public static $coroutineMode = ComponentInterface::COROUTINE_MODE_NEW;

    // 状态
    private $_status = ComponentInterface::STATUS_READY;

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

    // 请求前置事件
    public function onRequestBefore()
    {
        $this->setStatus(ComponentInterface::STATUS_RUNNING);
    }

    // 请求后置事件
    public function onRequestAfter()
    {
        $this->setStatus(ComponentInterface::STATUS_READY);
    }

}
