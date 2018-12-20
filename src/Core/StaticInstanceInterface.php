<?php

namespace Mix\Core;

/**
 * Interface StaticInstanceInterface
 * @author LIUJIAN <coder.keda@gmail.com>
 */
interface StaticInstanceInterface
{

    /**
     * 使用静态方法创建实例
     * @param mixed ...$args
     * @return $this
     */
    public static function new(...$args);

    /**
     * 创建实例，通过默认配置名
     * @return $this
     */
    public static function newInstance();

    /**
     * 创建实例，通过配置名
     * @param $name
     * @return $this
     */
    public static function newInstanceByName($name);

}
