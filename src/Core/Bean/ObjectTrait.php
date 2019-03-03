<?php

namespace Mix\Core\Bean;

use Mix\Core\StaticInstance\StaticInstanceTrait;

/**
 * Trait ObjectTrait
 * @package Mix\Core\Bean
 * @author LIUJIAN <coder.keda@gmail.com>
 */
trait ObjectTrait
{

    use StaticInstanceTrait;

    /**
     * 父级对象
     * @var mixed
     */
    public $parent;

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
        $config = \Mix::configure($this, $config);
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
