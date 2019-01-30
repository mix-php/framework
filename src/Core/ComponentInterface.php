<?php

namespace Mix\Core;

/**
 * Interface ComponentInterface
 * @author LIUJIAN <coder.keda@gmail.com>
 */
interface ComponentInterface
{

    // 协程模式值
    const COROUTINE_MODE_NEW = 0;
    const COROUTINE_MODE_REFERENCE = 1;

    // 状态值
    const STATUS_READY = 0;
    const STATUS_RUNNING = 1;

    // 获取状态
    public function getStatus();

    // 设置状态
    public function setStatus($status);

    // 前置处理事件
    public function onBeforeInitialize();

    // 后置处理事件
    public function onAfterInitialize();

}
