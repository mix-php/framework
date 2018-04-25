<?php

namespace mix\helpers;

/**
 * FilesystemHelper类
 * @author 刘健 <coder.liu@qq.com>
 */
class FilesystemHelper
{

    // 蛇形命名转换为驼峰命名
    public static function snakeToCamel($name, $ucfirst = false)
    {
        $name = ucwords(str_replace(['_', '-'], ' ', $name));
        $name = str_replace(' ', '', lcfirst($name));
        return $ucfirst ? ucfirst($name) : $name;
    }

    // 驼峰命名转换为蛇形命名
    public static function camelToSnake($name, $separator = '_')
    {
        $name = preg_replace_callback('/([A-Z]{1})/', function ($matches) use ($separator) {
            return $separator . strtolower($matches[0]);
        }, $name);
        if (substr($name, 0, 1) == $separator) {
            return substr($name, 1);
        }
        return $name;
    }

    // 返回路径中的目录部分，正反斜杠linux兼容处理
    public static function dirname($path)
    {
        if (strpos($path, '\\') === false) {
            return dirname($path);
        }
        return str_replace('/', '\\', dirname(str_replace('\\', '/', $path)));
    }

    // 返回路径中的文件名部分，正反斜杠linux兼容处理
    public static function basename($path)
    {
        if (strpos($path, '\\') === false) {
            return basename($path);
        }
        return str_replace('/', '\\', basename(str_replace('\\', '/', $path)));
    }

}
