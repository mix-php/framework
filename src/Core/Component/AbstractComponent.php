<?php

namespace Mix\Core\Component;

use Mix\Core\Bean\AbstractObject;

/**
 * Class AbstractComponent
 * @package Mix\Core\Component
 * @author LIUJIAN <coder.keda@gmail.com>
 */
abstract class AbstractComponent extends AbstractObject implements ComponentInterface
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
