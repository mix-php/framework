<?php

namespace Mix\Core\Container;

use Mix\Core\ComponentInterface;
use Mix\Core\BeanObject;

/**
 * Class Container
 * @package Mix\Core\Container
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Container extends BeanObject
{

    /**
     * 组件配置
     * @var ContainerManager
     */
    public $manager;

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
        $config = $this->manager->config;
        // 已加载
        if (isset($this->_instances[$name])) {
            return $this->_instances[$name];
        }
        // 未注册
        if (!isset($config[$name])) {
            throw new \Mix\Exceptions\ComponentException("Did not register this component: {$name}");
        }
        // 创建组件
        $object = \Mix::createComponent($config[$name]);
        // 组件效验
        if (!($object instanceof ComponentInterface)) {
            throw new \Mix\Exceptions\ComponentException("This class is not a component: {$config[$name]['class']}");
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
