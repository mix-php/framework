<?php

namespace Mix\Core\Application;

use Mix\Core\Component\ComponentInterface;

/**
 * Trait ComponentInitializeTrait
 * @package Mix\Http\Application
 * @author LIUJIAN <coder.keda@gmail.com>
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
        // 返回组件
        if ($component->getStatus() != ComponentInterface::STATUS_RUNNING) {
            return new ComponentBeforeInitialize($component, $name);
        }
        // 组件
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
        if ($component->getStatus() == ComponentInterface::STATUS_RUNNING) {
            $component->onAfterInitialize();
        }
    }

}
