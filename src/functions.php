<?php

/**
 * 助手函数
 * @author LIUJIAN <coder.keda@gmail.com>
 */

if (!function_exists('app')) {
    // 返回当前App实例
    function app()
    {
        return \Mix::$app;
    }
}

if (!function_exists('env')) {
    // 获取一个环境变量的值
    function env($name, $default = '')
    {
        return \Mix\Config\Environment::section($name, $default);
    }
}

if (!function_exists('xgo')) {
    // 创建一个带异常捕获的协程
    function xgo($closure)
    {
        \Mix\Core\Coroutine::create($closure);
    }
}

if (!function_exists('println')) {
    // 输出字符串并换行
    function println($strings)
    {
        echo $strings . PHP_EOL;
    }
}
