<?php

namespace mix\base;

/**
 * 对象基类
 * @author 刘健 <coder.liu@qq.com>
 */
class BaseObject
{

    // 构造
    public function __construct($attributes = [])
    {
        // 执行构造事件
        $this->onConstruct();
        // 导入属性
        foreach ($attributes as $name => $attribute) {
            $this->$name = $attribute;
        }
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
     * 创建实例
     * @param $name
     * @return $this
     */
    public static function newInstance($name = null)
    {
        // 直接实例化
        if (is_null($name)) {
            return new static();
        }
        // 根据配置实例化
        $class  = get_called_class();
        $object = \Mix::app()->createObject($name);
        if (get_class($object) != $class) {
            throw new \LibraryException('实例化类型与配置类型不符');
        }
        return $object;
    }

}
