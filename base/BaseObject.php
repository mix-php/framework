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

}
