<?php

/**
 * Mix类
 * @author 刘健 <coder.liu@qq.com>
 */
class Mix
{

    // 版本号
    const VERSION = '1.1.0-beta';

    // App实例
    protected static $_app;

    /**
     * 返回App，并设置组件命名空间
     *
     * @return \mix\http\Application|\mix\console\Application
     */
    public static function app($prefix = null)
    {
        // 获取App
        $app = self::getApp();
        // 设置组件命名空间
        $app->setComponentPrefix($prefix);
        // 返回App
        return $app;
    }

    /**
     * 获取App
     *
     * @return \mix\http\Application|\mix\console\Application
     */
    protected static function getApp()
    {
        return self::$_app;
    }

    // 设置App
    public static function setApp($app)
    {
        self::$_app = $app;
    }

    // 使用配置创建对象
    public static function createObject($config)
    {
        // 构建属性数组
        foreach ($config as $key => $value) {
            // 子类实例化
            if (is_array($value)) {
                // 实例化
                if (isset($value['class'])) {
                    $config[$key] = self::createObject($value);
                }
                // 引用其他组件
                if (isset($value['component'])) {
                    $componentPrefix = null;
                    $componentName   = $value['component'];
                    if (strpos($value['component'], '.') !== false) {
                        $fragments       = explode('.', $value['component']);
                        $componentName   = array_pop($fragments);
                        $componentPrefix = implode('.', $fragments);
                    }
                    $config[$key] = self::app($componentPrefix)->$componentName;
                }
            }
        }
        // 实例化
        $class = $config['class'];
        unset($config['class']);
        return new $class($config);
    }

}
