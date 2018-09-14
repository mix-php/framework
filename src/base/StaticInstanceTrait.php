<?php

namespace mix\base;

/**
 * Trait InstanceTrait
 * @author 刘健 <coder.liu@qq.com>
 */
trait StaticInstanceTrait
{

    /**
     * 创建实例，通过配置名
     * @param $name
     * @return $this
     */
    public static function newInstanceByConfig($name)
    {
        $class  = get_called_class();
        $config = app()->config($name);
        $object = create_object($config);
        if (get_class($object) != $class) {
            throw new \mix\exceptions\ConfigException('实例化类型与配置类型不符');
        }
        return $object;
    }

}
