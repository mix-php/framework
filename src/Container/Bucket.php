<?php

namespace Mix\Container;

use Mix\Core\ComponentInterface;
use Mix\Core\DIObject;

/**
 * 存储空间类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Bucket extends DIObject
{

    /**
     * 组件配置
     * @var Container
     */
    public $container;

    /**
     * 容器中的对象实例
     * @var array
     */
    protected $_instances = [];

    /**
     * 获取容器
     * @param $name
     * @return ComponentInterface
     */
    public function get($name)
    {
        $config = $this->container->config;
        // 已加载
        if (isset($this->_instances[$name])) {
            return $this->_instances[$name];
        }
        // 未注册
        if (!isset($config[$name])) {
            throw new \Mix\Exceptions\ComponentException("组件不存在：{$name}");
        }
        // 使用配置创建新对象
        $object = \Mix::createObject($config[$name]);
        // 组件效验
        if (!($object instanceof ComponentInterface)) {
            throw new \Mix\Exceptions\ComponentException("不是组件类型：{$config[$name]['class']}");
        }
        // 装入容器
        $this->_instances[$name] = $object;
        // 返回
        return $this->_instances[$name];
    }

    /**
     * 判断容器是否存在
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->_instances[$name]);
    }

}
