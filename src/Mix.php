<?php

/**
 * Mix类
 * @author 刘健 <coder.liu@qq.com>
 */
class Mix
{

    // 命令行模式
    const MODE_CLI = 0;

    // 服务器模式
    const MODE_SERVER = 1;

    // 协程服务器模式
    const MODE_SERVER_COROUTINE = 2;

    // App实例
    protected static $_app;

    // App实例集合
    protected static $_apps;

    // App实例集合(协程)
    public static $_coroutineApps;

    // 主机
    protected static $_host;

    // 虚拟主机配置
    protected static $_virtualHosts;

    // 当前执行模式
    public static $runMode = self::MODE_CLI;

    // 公共容器
    public static $container;

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
        // 返回协程 HTTP 应用
        if (isset(self::$_coroutineApps)) {
            self::$runMode = self::MODE_SERVER_COROUTINE;
            return self::$_coroutineApps[\Swoole\Coroutine::getuid()];
        }
        // 返回 HTTP 应用
        if (isset(self::$_apps)) {
            self::$runMode = self::MODE_SERVER;
            return self::$_apps[self::$_host];
        }
        // 返回命令行应用
        if (isset(self::$_app)) {
            self::$runMode = self::MODE_CLI;
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
    public static function selectHost($host, $enableCoroutine = false)
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
        self::createApplication(self::$_host, $enableCoroutine);
        // 创建协程App
        if ($enableCoroutine) {
            self::createCoroutineApplication();
        }
    }

    // 动态实例化App
    protected static function createApplication($host, $enableCoroutine)
    {
        // 动态实例化App
        if (isset(self::$_apps[$host])) {
            return;
        }
        $configFile = self::$_virtualHosts[$host];
        $config     = require $configFile;
        $app        = new \mix\http\Application($config);
        if ($enableCoroutine) {
            $app->loadCoroutineShareComponents();
        } else {
            $app->loadAllComponents();
        }
        self::$_apps[$host] = $app;
    }

    // 创建协程App
    protected static function createCoroutineApplication()
    {
        $coroutineId                        = \Swoole\Coroutine::getuid();
        $coroutineApp                       = clone self::$_apps[self::$_host];
        self::$_coroutineApps[$coroutineId] = $coroutineApp;
    }

    // 删除协程App
    public static function removeCoroutineApplication()
    {
        if (self::$runMode != self::MODE_SERVER_COROUTINE) {
            return;
        }
        $coroutineId                        = \Swoole\Coroutine::getuid();
        self::$_coroutineApps[$coroutineId] = null;
    }

    // 使用配置创建对象
    public static function createObject($config)
    {
        // 构建属性数组
        foreach ($config as $key => $value) {
            // 子类实例化
            if (is_array($value) && isset($value['class'])) {
                $subClass = $value['class'];
                unset($value['class']);
                $config[$key] = new $subClass($value);
            }
        }
        // 实例化
        $class = $config['class'];
        unset($config['class']);
        return new $class($config);
    }

}
