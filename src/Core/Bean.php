<?php

namespace Mix\Core;

use Mix\Helpers\FileSystemHelper;

/**
 * Class Bean
 * @package Mix\Core
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Bean
{

    /**
     * 解析后的配置
     * @var array
     */
    public static $config;

    /**
     * 载入配置
     */
    public static function loadConfig()
    {
        $config = \Mix::$app->beans;
        $data   = [];
        foreach ($config as $item) {
            if (!isset($item['class'])) {
                continue;
            }
            if (isset($item['name'])) {
                $name = $item['name'];
            } else {
                $name = self::name($item['class']);
            }
            $data[$name] = $item;
        }
        self::$config = $data;
    }

    /**
     * 获取配置
     * @param $bean
     * @return array
     */
    public static function config($name)
    {
        if (!isset(self::$config)) {
            self::loadConfig();
        }
        if (!isset(self::$config[$name])) {
            throw new \Mix\Exceptions\ConfigException("Bean configuration not found: {$name}");
        }
        return self::$config[$name];
    }

    /**
     * 获取Bean名称
     * @param $class
     * @return string
     */
    public static function name($class)
    {
        return md5($class);
    }

}
