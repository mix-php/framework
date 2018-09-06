<?php

namespace mix\http;

use mix\base\BaseObject;
use mix\facades\Output;
use mix\helpers\ProcessHelper;

/**
 * Http服务器类
 * @author 刘健 <coder.liu@qq.com>
 */
class HttpServer extends BaseObject
{

    // 虚拟主机
    public $virtualHost = [];

    // 运行时的各项参数
    public $settings = [];

    // 服务器
    protected $_server;

    // 主机
    protected $_host;

    // 端口
    protected $_port;

    // 初始化事件
    public function onInitialize()
    {
        parent::onInitialize();
        // 赋值
        $this->_host = $this->virtualHost['host'];
        $this->_port = $this->virtualHost['port'];
        // 实例化服务器
        $this->_server = new \Swoole\Http\Server($this->_host, $this->_port);
    }

    // 启动服务
    public function start()
    {
        $this->welcome();
        $this->onStart();
        $this->onManagerStart();
        $this->onWorkerStart();
        $this->onRequest();
        $this->_server->set($this->settings);
        $this->_server->start();
    }

    // 主进程启动事件
    protected function onStart()
    {
        $this->_server->on('Start', function ($server) {
            // 进程命名
            ProcessHelper::setTitle("mix-httpd: master {$this->_host}:{$this->_port}");
        });
    }

    // 管理进程启动事件
    protected function onManagerStart()
    {
        $this->_server->on('ManagerStart', function ($server) {
            // 进程命名
            ProcessHelper::setTitle("mix-httpd: manager");
        });
    }

    // 工作进程启动事件
    protected function onWorkerStart()
    {
        $this->_server->on('WorkerStart', function ($server, $workerId) {
            // 进程命名
            if ($workerId < $server->setting['worker_num']) {
                ProcessHelper::setTitle("mix-httpd: worker #{$workerId}");
            } else {
                ProcessHelper::setTitle("mix-httpd: task #{$workerId}");
            }
            // 实例化App
            $config = require $this->virtualHost['configFile'];
            $app    = new \mix\http\Application($config);
            $app->loadAllComponents();
        });
    }

    // 请求事件
    protected function onRequest()
    {
        $this->_server->on('request', function ($request, $response) {
            // 执行请求
            try {
                \Mix::app()->request->setRequester($request);
                \Mix::app()->response->setResponder($response);
                \Mix::app()->run();
            } catch (\Throwable $e) {
                \Mix::app()->error->handleException($e);
            }
        });
    }

    // 欢迎信息
    protected function welcome()
    {
        $swooleVersion = swoole_version();
        $phpVersion    = PHP_VERSION;
        echo <<<EOL
                           _____
_______ ___ _____ ___ _____  / /_  ____
__/ __ `__ \/ /\ \/ / / __ \/ __ \/ __ \
_/ / / / / / / /\ \/ / /_/ / / / / /_/ /
/_/ /_/ /_/_/ /_/\_\/ .___/_/ /_/ .___/
                   /_/         /_/


EOL;
        Output::writeln('Server      Name: mix-httpd');
        Output::writeln('Framework   Version: ' . \Mix::VERSION);
        Output::writeln("PHP         Version: {$phpVersion}");
        Output::writeln("Swoole      Version: {$swooleVersion}");
        Output::writeln("Listen      Addr: {$this->_host}");
        Output::writeln("Listen      Port: {$this->_port}");
        Output::writeln('Coroutine   Mode: ' . ($this->settings['enable_coroutine'] ? 'enable' : 'disable'));
        Output::writeln("Config      File: {$this->virtualHost['configFile']}");
    }

}
