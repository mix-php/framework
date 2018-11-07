<?php

/**
 * Mix类
 * @author 刘健 <coder.liu@qq.com>
 */
class Mix
{

    // 版本号
    const VERSION = '1.1.1';

    /**
     * App实例
     * @var \Mix\Http\Application|\Mix\Console\Application
     */
    public static $app;

    // 构建配置
    public static function configure($config, $newInstance = false)
    {
        foreach ($config as $key => $value) {
            // 子类实例化
            if (is_array($value)) {
                // 实例化
                if (isset($value['class'])) {
                    $config[$key] = self::configure($value, true);
                }
                // 引用其他组件
                if (isset($value['component'])) {
                    $name         = $value['component'];
                    $config[$key] = self::$app->$name;
                }
            }
        }
        if ($newInstance) {
            $class = $config['class'];
            unset($config['class']);
            return new $class($config);
        }
        return $config;
    }

    // 导入属性
    public static function importAttributes($object, $config)
    {
        foreach ($config as $name => $value) {
            $object->$name = $value;
        }
        return $object;
    }

    // 使用配置创建对象
    public static function createObject($config)
    {
        $class = $config['class'];
        unset($config['class']);
        return new $class($config);
    }

}
