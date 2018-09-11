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
     * @param null $name
     * @param string $parent
     * @return $this
     */
    public static function newInstanceByConfig($name = null, $parent = StaticInstanceInterface::CONFIG_LIBRARIES)
    {
        $class  = get_called_class();
        $config = app()->config("{$parent}.[{$name}]");
        $object = create_object($config);
        if (get_class($object) != $class) {
            throw new \ConfigException('实例化类型与配置类型不符');
        }
        return $object;
    }

}
