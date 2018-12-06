<?php

namespace Mix\Core;

/**
 * Interface StaticInstanceInterface
 * @author 刘健 <coder.liu@qq.com>
 */
interface StaticInstanceInterface
{

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
