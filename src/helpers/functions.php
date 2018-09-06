<?php

/**
 * 助手函数
 * @author 刘健 <coder.liu@qq.com>
 */

if (!function_exists('app')) {
    // 返回当前 App 实例
    function app($prefix = null)
    {
        return \Mix::app($prefix);
    }
}

if (!function_exists('create_object')) {
    // 使用配置创建对象
    function create_object($config)
    {
        return \Mix::createObject($config);
    }
}

if (!function_exists('env')) {
    // 获取一个环境变量的值
    function env($name)
    {
        return \mix\base\Env::get($name);
    }
}

if (!function_exists('tgo')) {
    // 创建一个带异常捕获的协程
    function tgo($closure)
    {
        go(function () use ($closure) {
            try {
                $closure();
            } catch (\Throwable $e) {
                // 输出错误并退出
                \Mix::app()->error->handleException($e, true);
            }
        });
    }
}
