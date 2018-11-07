<?php

namespace Mix\Container;

use Mix\Core\ComponentInterface;

/**
 * 基础容器类
 * @author 刘健 <coder.liu@qq.com>
 */
class BaseContainer extends BaseObject
{

    /**
     * 组件配置
     * @var array
     */
    protected $config = [];

    /**
     * 容器中的对象实例
     * @var array
     */
    protected $instances = [];

    /**
     * 获取容器
     * @param $name
     * @return ComponentInterface
     */
    public function get($name)
    {
        // 已加载
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }
        // 未注册
        if (!isset($this->config[$name])) {
            throw new \Mix\Exceptions\ComponentException("组件不存在：{$name}");
        }
        // 使用配置创建新对象
        $object = \Mix::createObject($this->config[$name]);
        // 组件效验
        if (!($object instanceof ComponentInterface)) {
            throw new \Mix\Exceptions\ComponentException("不是组件类型：{$this->config[$name]['class']}");
        }
        // 装入容器
        $this->instances[$name] = $object;
        // 返回
        return $this->instances[$name];
    }

    /**
     * 判断容器是否存在
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->instances[$name]);
    }

}
