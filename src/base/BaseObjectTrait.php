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

    // 析构
    public function __destruct()
    {
        $this->onDestruct();
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

}
