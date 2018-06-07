<?php

/**
 * 助手函数
 * @author 刘健 <coder.liu@qq.com>
 */

if (!function_exists('app')) {
    // 返回当前 App 实例
    function app($componentNamespace = null)
    {
        return \Mix::app($componentNamespace);
    }
}

if (!function_exists('create_object')) {
    // 使用配置创建对象
    function create_object($config)
    {
        return \Mix::createObject($config);
    }
}
