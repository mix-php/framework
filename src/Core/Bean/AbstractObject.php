<?php

namespace Mix\Core\Bean;

use Mix\Core\Bean;

/**
 * Class AbstractObject
 * @package Mix\Core\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
abstract class AbstractObject implements ObjectInterface
{

    /**
     * 构造
     * BeanObject constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        // 执行构造事件
        $this->onConstruct();
        // 构建配置
        $config = \Mix::configure($config);
        // 导入属性
        \Mix::importProperties($this, $config);
        // 执行初始化事件
        $this->onInitialize();
    }

    /**
     * 析构
     */
    public function __destruct()
    {
        $this->onDestruct();
    }

    /**
     * 构造事件
     */
    public function onConstruct()
    {
    }

    /**
     * 初始化事件
     */
    public function onInitialize()
    {
    }

    /**
     * 析构事件
     */
    public function onDestruct()
    {
    }

    /**
     * 使用依赖创建实例
     * @param $name
     * @return $this
     */
    public static function newInstance($name = null)
    {
        $currentClass = get_called_class();
        $bean         = Bean::config(is_null($name) ? Bean::name($currentClass) : $name);
        $class        = $bean['class'];
        $properties   = $bean['properties'] ?? [];
        if ($class != $currentClass) {
            throw new \Mix\Exception\ConfigException("Bean class is not equal to the current class, Current class: {$currentClass}, Bean class: {$class}");
        }
        return new $class($properties);
    }

    /**
     * 通过对象创建实例
     * 为了实现类型的代码补全
     * @param $object
     * @return $this
     */
    public static function make($object)
    {
        $currentClass = get_called_class();
        $class        = get_class($object);
        if ($currentClass != $class) {
            throw new \RuntimeException("Type mismatch: Current class: {$currentClass}, Parameter class: {$class}");
        }
        return $object;
    }

}
