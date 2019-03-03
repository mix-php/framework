<?php

namespace Mix\Core\Bean;

use Mix\Core\StaticInstance\StaticInstanceInterface;

/**
 * Interface ObjectInterface
 * @package Mix\Core\Bean
 * @author LIUJIAN <coder.keda@gmail.com>
 */
interface ObjectInterface extends StaticInstanceInterface
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

}
