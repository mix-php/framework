<?php

namespace Mix\Core\Component;

use Mix\Core\Bean\AbstractObject;

/**
 * Class AbstractComponent
 * @package Mix\Core\Component
 * @author liu,jian <coder.keda@gmail.com>
 */
abstract class AbstractComponent extends AbstractObject implements ComponentInterface
{
    
    /**
     * 协程模式
     * @var int
     */
    const COROUTINE_MODE = ComponentInterface::COROUTINE_MODE_NEW;

    /**
     * 组件状态
     * @var int
     */
    private $_status = ComponentInterface::STATUS_READY;

    /**
     * 获取组件状态
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * 设置组件状态
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->_status = $status;
    }

    /**
     * 前置处理事件
     */
    public function onBeforeInitialize()
    {
        $this->setStatus(ComponentInterface::STATUS_RUNNING);
    }

    /**
     * 后置处理事件
     */
    public function onAfterInitialize()
    {
        $this->setStatus(ComponentInterface::STATUS_READY);
    }

}
