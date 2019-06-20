<?php

/**
 * 助手函数
 * @author liu,jian <coder.keda@gmail.com>
 */

if (!function_exists('app')) {
    // 返回当前App实例
    function app()
    {
        return \Mix::$app;
    }
}

if (!function_exists('server')) {
    // 返回当前Server实例
    function server()
    {
        return \Mix::$server;
    }
}

if (!function_exists('env')) {
    // 获取一个环境变量的值
    function env($name, $default = '')
    {
        return \Mix::$env->section($name, $default);
    }
}

if (!function_exists('beanname')) {
    // 获取Bean名称
    function beanname($class)
    {
        return \Mix\Bean\Beans::name($class);
    }
}

if (!function_exists('xgo')) {
    // 创建协程
    function xgo($function, ...$params)
    {
        \Mix\Concurrent\Coroutine::create($function, ...$params);
    }
}

if (!function_exists('xdefer')) {
    // 创建延迟执行
    function xdefer($function)
    {
        return \Swoole\Coroutine::defer($function);
    }
}

if (!function_exists('println')) {
    // 输出字符串并换行
    function println($strings)
    {
        echo $strings . PHP_EOL;
    }
}
