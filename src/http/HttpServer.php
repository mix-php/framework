<?php

namespace mix\http;

use mix\base\BaseObject;
use mix\helpers\ProcessHelper;

/**
 * Http服务器类
 * @author 刘健 <coder.liu@qq.com>
 */
class HttpServer extends BaseObject
{

    // 主机
    public $host;

    // 端口
    public $port;

    // 运行时的各项参数
    public $settings = [];

    // 虚拟主机
    public $virtualHosts = [];

    // Server对象
    protected $_server;

    // 初始化事件
    public function onInitialize()
    {
        parent::onInitialize();
        // 实例化服务器
        $this->_server = new \Swoole\Http\Server($this->host, $this->port);
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
            ProcessHelper::setTitle("mix-httpd: master {$this->host}:{$this->port}");
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
            // 设置虚拟主机配置
            \Mix::setVirtualHosts($this->virtualHosts);
        });
    }

    // 请求事件
    protected function onRequest()
    {
        $this->_server->on('request', function ($request, $response) {
            // 切换当前host
            $host = isset($request->header['host']) ? $request->header['host'] : '';
            \Mix::setHost($host);
            // 执行请求
            try {
                \Mix::app()->request->setRequester($request);
                \Mix::app()->response->setResponder($response);
                \Mix::app()->run();
            } catch (\Exception $e) {
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
        self::send('Server    Name: mix-httpd');
        self::send("PHP    Version: {$phpVersion}");
        self::send("Swoole Version: {$swooleVersion}");
        self::send("Listen    Addr: {$this->host}");
        self::send("Listen    Port: {$this->port}");
    }

    // 发送至屏幕
    protected static function send($msg)
    {
        $time = date('Y-m-d H:i:s');
        echo "[{$time}] " . $msg . PHP_EOL;
    }

}
