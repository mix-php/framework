<?php

namespace Mix\Core;

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
    public static function newInstanceByConfig($name = 'default')
    {
        $class  = get_called_class();
        $config = \Mix::$app->libraries;
        if (!isset($config["{$class}:{$name}"])) {
            throw new \Mix\Exceptions\ConfigException("配置不存在：{$class} {$name}");
        }
        return \Mix::createObject($config["{$class}:{$name}"]);
    }

}
