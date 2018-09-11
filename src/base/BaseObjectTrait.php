<?php

namespace mix\base;

/**
 * 对象基类Trait
 * @author 刘健 <coder.liu@qq.com>
 */
trait BaseObjectTrait
{

    // 构造
    public function __construct($config = [])
    {
        // 执行构造事件
        $this->onConstruct();
        // 导入属性
        \Mix::configure($this, $config);
        // 执行初始化事件
        $this->onInitialize();
    }

    // 构造事件
    public function onConstruct()
    {
    }

    // 初始化事件
    public function onInitialize()
    {
    }

    // 析构事件
    public function onDestruct()
    {
    }

    // 析构
    public function __destruct()
    {
        $this->onDestruct();
    }

    /**
     * 创建实例，通过配置名
     * @param null $name
     * @param string $parent
     * @return $this
     */
    public static function newInstanceByConfig($name = null, $parent = self::CONFIG_LIBRARIES)
    {
        $class  = get_called_class();
        $config = app()->config("{$parent}.[{$name}]");
        $object = create_object($config);
        if (get_class($object) != $class) {
            throw new \ConfigException('实例化类型与配置类型不符');
        }
        return $object;
    }

}
