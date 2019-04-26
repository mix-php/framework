<?php

namespace Mix\Core\Application;

use Mix\Core\Component\ComponentInterface;

/**
 * Trait ComponentInitializeTrait
 * @package Mix\Http\Application
 * @author liu,jian <coder.keda@gmail.com>
 */
trait ComponentInitializeTrait
{

    /**
     * 获取组件
     * @param $name
     * @return \Mix\Core\Component\ComponentInterface
     */
    public function __get($name)
    {
        $component = $this->container->get($name);
        // 触发前置处理事件
        self::triggerBeforeInitialize($component);
        // 修改引用组件状态
        if (self::getCoroutineMode($component) == ComponentInterface::COROUTINE_MODE_REFERENCE && $component->getStatus() == ComponentInterface::STATUS_READY) {
            $component->setStatus(ComponentInterface::STATUS_RUNNING);
        }
        // 停用未初始化的组件
        if ($component->getStatus() != ComponentInterface::STATUS_RUNNING) {
            return new ComponentDisabled($component, $name);
        }
        // 返回组件
        return $component;
    }

    /**
     * 清扫组件容器
     */
    public function cleanComponents()
    {
        // 触发后置处理事件
        foreach (array_keys($this->components) as $name) {
            if (!$this->container->has($name)) {
                continue;
            }
            $component = $this->container->get($name);
            self::triggerAfterInitialize($component);
        }
    }

    /**
     * 触发前置处理事件
     * @param $component
     */
    protected static function triggerBeforeInitialize($component)
    {
        if (self::getCoroutineMode($component) == ComponentInterface::COROUTINE_MODE_REFERENCE) {
            return;
        }
        if ($component->getStatus() == ComponentInterface::STATUS_READY) {
            $component->onBeforeInitialize();
        }
    }

    /**
     * 触发后置处理事件
     * @param $component
     */
    protected static function triggerAfterInitialize($component)
    {
        if (self::getCoroutineMode($component) == ComponentInterface::COROUTINE_MODE_REFERENCE) {
            return;
        }
        if ($component->getStatus() == ComponentInterface::STATUS_RUNNING) {
            $component->onAfterInitialize();
        }
    }

    /**
     * 获取协程模式
     * @param $component
     * @return bool
     */
    protected static function getCoroutineMode($component)
    {
        $class = get_class($component);
        return $class::COROUTINE_MODE ?? false;
    }

}
