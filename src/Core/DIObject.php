<?php

namespace Mix\Core;

/**
 * 依赖注入对象基类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
abstract class DIObject implements StaticInstanceInterface
{

    use StaticInstanceTrait;

    /**
     * 构造
     * DIObject constructor.
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

}
