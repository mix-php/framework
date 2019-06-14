<?php

namespace Mix\Core\Component;

/**
 * Interface ComponentInterface
 * @package Mix\Core\Component
 * @author liu,jian <coder.keda@gmail.com>
 */
interface ComponentInterface
{

    /**
     * 协程模式值
     */
    const COROUTINE_MODE_NEW = 0;
    const COROUTINE_MODE_REFERENCE = 1;

    /**
     * 组件状态值
     */
    const STATUS_READY = 0;
    const STATUS_RUNNING = 1;

    /**
     * 协程模式
     * @var int
     */
    const COROUTINE_MODE = ComponentInterface::COROUTINE_MODE_NEW;

    /**
     * 获取组件状态
     * @return int
     */
    public function getStatus();

    /**
     * 设置组件状态
     * @param int $status
     */
    public function setStatus(int $status);

    /**
     * 前置处理事件
     */
    public function onBeforeInitialize();

    /**
     * 后置处理事件
     */
    public function onAfterInitialize();

}
