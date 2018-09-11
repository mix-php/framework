<?php

namespace mix\base;

/**
 * Interface InstanceInterface
 * @author 刘健 <coder.liu@qq.com>
 */
interface StaticInstanceInterface
{

    // 配置类型值
    const CONFIG_COMPONENTS = 'components';
    const CONFIG_LIBRARIES = 'libraries';

    /**
     * 创建实例，通过配置名
     * @param null $name
     * @param string $parent
     * @return $this
     */
    public static function newInstanceByConfig($name = null, $parent = StaticInstanceInterface::CONFIG_LIBRARIES);

}
