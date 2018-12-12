<?php

namespace Mix\Config;

/**
 * Class Environment
 * @package Mix\Config
 * @author 刘健 <coder.liu@qq.com>
 */
class Environment
{

    // 数据
    protected static $_data = [];

    // 加载环境配置
    public static function load($file)
    {
        $iniParser = new INIParser();
        $iniParser->load($file);
        $config      = $iniParser->sections();
        self::$_data = array_merge($config, $_SERVER, $_ENV);
    }

    // 获取配置
    public static function section($name, $default = '')
    {
        $current   = self::$_data;
        $fragments = explode('.', $name);
        foreach ($fragments as $key) {
            if (!isset($current[$key])) {
                return $default;
            }
            $current = $current[$key];
        }
        return $current;
    }

    // 返回全部数据
    public static function sections()
    {
        return self::$_data;
    }

}
