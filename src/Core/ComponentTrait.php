<?php

namespace Mix\Core;

/**
 * Trait ComponentTrait
 * @author LIUJIAN <coder.keda@gmail.com>
 */
trait ComponentTrait
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

    // 前置处理事件
    public function onBeforeInitialize()
    {
        $this->setStatus(ComponentInterface::STATUS_RUNNING);
    }

    // 后置处理事件
    public function onAfterInitialize()
    {
        $this->setStatus(ComponentInterface::STATUS_READY);
    }

}
