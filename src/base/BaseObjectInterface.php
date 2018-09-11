<?php

namespace mix\base;

/**
 * 对象基类Interface
 * @author 刘健 <coder.liu@qq.com>
 */
interface BaseObjectInterface
{

    // 构造
    public function __construct($config = []);

    // 析构
    public function __destruct();

    // 构造事件
    public function onConstruct();

    // 初始化事件
    public function onInitialize();

    // 析构事件
    public function onDestruct();

}
