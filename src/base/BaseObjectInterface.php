<?php

namespace mix\base;

/**
 * 对象基类Interface
 * @author 刘健 <coder.liu@qq.com>
 */
interface  BaseObjectInterface
{

    // 配置类型值
    const CONFIG_COMPONENTS = 'components';
    const CONFIG_LIBRARIES = 'libraries';

    // 构造
    public function __construct($config = []);

    // 构造事件
    public function onConstruct();

    // 初始化事件
    public function onInitialize();

    // 析构事件
    public function onDestruct();

    // 析构
    public function __destruct();

    /**
     * 创建实例，通过配置名
     * @param null $name
     * @param string $parent
     * @return $this
     */
    public static function newInstanceByConfig($name = null, $parent = self::CONFIG_LIBRARIES);

}
