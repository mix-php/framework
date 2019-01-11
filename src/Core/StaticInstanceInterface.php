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
     * 使用依赖创建实例
     * @param $name
     * @return $this
     */
    public static function newInstance($name = null);

}
