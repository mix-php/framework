<?php

/**
 * Mix类
 * @author 刘健 <coder.liu@qq.com>
 */
class Mix
{

    // App实例
    protected static $_app;

    // App实例集合
    protected static $_apps;

    // 主机
    protected static $_host;

    // 虚拟主机配置
    protected static $_virtualHosts;

    /**
     * 返回App，并设置组件命名空间
     *
     * @return \mix\http\Application|\mix\console\Application
     */
    public static function app($componentNamespace = null)
    {
        // 获取App
        $app = self::getApp();
        if (is_null($app)) {
            return $app;
        }
        // 设置组件命名空间
        $app->setComponentNamespace($componentNamespace);
        // 返回App
        return $app;
    }

    /**
     * 获取App
     *
     * @return \mix\http\Application|\mix\console\Application
     */
    protected static function getApp()
    {
        // 返回 HTTP 应用
        if (isset(self::$_apps)) {
            return self::$_apps[self::$_host];
        }
        // 返回命令行应用
        if (isset(self::$_app)) {
            return self::$_app;
        }
        return null;
    }

    // 设置App
    public static function setApp($app)
    {
        self::$_app = $app;
    }

    // 设置虚拟主机配置
    public static function setVirtualHosts($virtualHosts)
    {
        self::$_virtualHosts = $virtualHosts;
    }

    // 切换当前Host
    public static function selectHost($host)
    {
        // 切换当前Host
        self::$_host  = null;
        $virtualHosts = self::$_virtualHosts;
        foreach ($virtualHosts as $virtualHost => $configFile) {
            if ($virtualHost == '*') {
                continue;
            }
            if (preg_match("/{$virtualHost}/i", $host)) {
                self::$_host = $virtualHost;
                break;
            }
        }
        if (is_null(self::$_host)) {
            self::$_host = isset($virtualHosts['*']) ? '*' : array_shift($virtualHosts);
        }
        // 动态实例化App
        self::createApplication(self::$_host);
    }

    // 动态实例化App
    protected static function createApplication($host)
    {
        // 动态实例化App
        if (isset(self::$_apps[$host])) {
            return;
        }
        $configFile = self::$_virtualHosts[$host];
        $config     = require $configFile;
        $app        = new \mix\http\Application($config);
        $app->loadAllComponents();
        self::$_apps[$host] = $app;
    }

    // 使用配置创建对象
    public static function createObject($config)
    {
        // 构建属性数组
        foreach ($config as $key => $value) {
            // 子类实例化
            if (is_array($value)) {
                // 实例化
                if (isset($value['class'])) {
                    $config[$key] = self::createObject($value);
                }
                // 引用其他组件
                if (isset($value['component'])) {
                    $componentNamespace = null;
                    $componentName      = $value['component'];
                    if (strpos($value['component'], '.') !== false) {
                        list($componentNamespace, $componentName) = explode('.', $value['component']);
                    }
                    $config[$key] = self::app($componentNamespace)->$componentName;
                }
            }
        }
        // 实例化
        $class = $config['class'];
        unset($config['class']);
        return new $class($config);
    }

}
