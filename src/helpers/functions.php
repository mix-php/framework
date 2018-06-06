<?php

/**
 * 助手函数
 * @author 刘健 <coder.liu@qq.com>
 */

if (!function_exists('value')) {
    // 返回当前 App 实例
    function app($componentNamespace = null)
    {
        return \Mix::app($componentNamespace);
    }
}
