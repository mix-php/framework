<?php

/**
 * Class Mix
 * @author liu,jian <coder.keda@gmail.com>
 */
class Mix
{

    /**
     * 版本号
     * @var string
     */
    public static $version = '2.1.0-alpha';

    /**
     * App实例
     * @var \Mix\Console\Application
     */
    public static $console;

    /**
     * 环境配置
     * @var \Mix\Core\Environment
     */
    public static $env;

    /**
     * 从文件载入环境配置
     * @param $filename
     * @return bool
     */
    public static function loadEnvironmentFrom($filename)
    {
        $env = new \Mix\Core\Environment(['filename' => $filename]);
        $env->load();
        self::$env = $env;
        return true;
    }

}
