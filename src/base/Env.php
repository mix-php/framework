<?php

namespace mix\base;

use mix\exceptions\EnvException;

/**
 * 环境类
 * @author 刘健 <coder.liu@qq.com>
 */
class Env
{

    // ENV 参数
    protected static $_env = [];

    // 加载环境配置
    public static function load($envFile)
    {
        if (!is_file($envFile)) {
            throw new EnvException('Environment file does not exist.');
        }
        $data       = parse_ini_file($envFile);
        self::$_env = array_merge($_ENV, $data);
    }

    // 获取配置
    public static function get($name = null)
    {
        if (is_null($name)) {
            return self::$_env;
        }
        if (!isset(self::$_env[$name])) {
            throw new EnvException("Environment config does not exist: {$name}.");
        }
        return self::$_env[$name];
    }

}
