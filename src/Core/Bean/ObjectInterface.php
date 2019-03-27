<?php

namespace Mix\Core\Bean;

/**
 * Interface ObjectInterface
 * @package Mix\Core\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
interface ObjectInterface
{

    /**
     * 构造
     * BeanObject constructor.
     * @param array $config
     */
    public function __construct($config = []);

    /**
     * 析构
     */
    public function __destruct();

    /**
     * 构造事件
     */
    public function onConstruct();

    /**
     * 初始化事件
     */
    public function onInitialize();

    /**
     * 析构事件
     */
    public function onDestruct();

    /**
     * 使用依赖创建实例
     * @param $name
     * @return $this
     */
    public static function newInstance($name = null);

}
